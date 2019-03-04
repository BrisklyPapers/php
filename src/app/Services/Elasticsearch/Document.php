<?php

namespace App\Services\Elasticsearch;

use App\Models\Elasticsearch\Attachment;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class Document
{
    const TYPE = 'documents';
    const FIELD_DOCUMENT = 'file';
    const FIELD_TAGS = 'tags';
    const FIELD_TEXT = 'text';

    /**
     * @var Client
     */
    private $elastic;

    /**
     * Document constructor.
     * @param Client $elastic
     */
    public function __construct(Client $elastic)
    {
        $this->elastic = $elastic;
    }

    protected function getIndexName()
    {
        return 'brisklypapers' . config('elasticsearch.index_prefix');
    }

    /**
     * @param Attachment $document
     * @param array $tags
     *
     * @return array
     */
    public function storeDocument(Attachment $document, array $tags = [])
    {
        $params = [
            'index' => $this->getIndexName(),
            'type' => self::TYPE,
            'body' => [
                self::FIELD_DOCUMENT => [
                    '_content' => base64_encode($document->content),
                    '_content_type' => $document->contentType,
                    '_name' => $document->fileName
                ],
                self::FIELD_TEXT => $document->text,
                self::FIELD_TAGS => $tags
            ]
        ];

        if (!empty($document->id)) {
            $params['id'] = $document->id;
        }

        $response = $this->elastic->index($params);

        return $response;
    }

    /**
     * @param string $id
     * @return Attachment
     */
    public function loadById($id)
    {
        $params = [
            'index' => $this->getIndexName(),
            'type' => self::TYPE,
            'id' => $id,
        ];

        try {
            $response = $this->elastic->get($params);
        } catch(Missing404Exception $e) {
            $response = ['found' => false];
        }

        if (!$response['found']) {
            throw new \InvalidArgumentException("$id not found");
        }

        $attachment = $this->createAttachment($response);

        return $attachment;
    }

    /**
     * @param string $text
     * @return Attachment[]
     */
    public function search($text)
    {
        $attachments = [];

        $params = [
            'index' => $this->getIndexName(),
            'type' => self::TYPE,
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'multi_match' => [
                                    'query' => $text,
                                    'type' => 'best_fields',
                                    //'fuzziness' => 'AUTO',
                                    'fields' => [self::FIELD_TEXT]
                                ]
                            ],
                            [
                                'multi_match' => [
                                    'query' => $text,
                                    'type' => 'best_fields',
                                    //'fuzziness' => 'AUTO',
                                    'fields' => [self::FIELD_TAGS]
                                ]
                            ]
                        ]
                    ]
                ],
                'highlight' => [
                    'fields' => [
                        self::FIELD_TEXT => (object) []
                    ]
                ]
            ],
        ];

        $result = $this->elastic->search($params);

        if (isset($result['hits']['hits'])) {
            foreach ($result['hits']['hits'] as $row) {
                $attachments[] = $this->createAttachment($row);
            }
        }

        return $attachments;
    }

    /**
     * @param $response
     * @return Attachment[]
     */
    private function createAttachment($response)
    {
        $attachment = new Attachment();
        $attachment->content = base64_decode($response['_source']['file']['_content']);
        $attachment->contentType = $response['_source']['file']['_content_type'];
        $attachment->fileName = $response['_source']['file']['_name'];
        $attachment->text = $response['_source']['text'];
        if (isset($response['highlight']['text'])) {
            $attachment->highlighted = $response['highlight']['text'];
        }
        $attachment->tags = $response['_source']['tags'];
        $attachment->id = isset($response['_id']) ? $response['_id'] : null;

        return $attachment;
    }

    public function searchTags($text)
    {
        $params = [
            'index' => $this->getIndexName(),
            'type' => self::TYPE,
            'body' => [
                'size' => 0,
                'aggs' => [
                    'tag_names' => [
                        'terms' => ['field' => self::FIELD_TAGS],
                    ]
                ]
            ],
        ];

        $result = $this->elastic->search($params);

        $lowerText = mb_strtolower($text);
        $strlen = mb_strlen($text);

        $tags = array();
        if (isset($result['aggregations']['tag_names']['buckets'])) {
            foreach ($result['aggregations']['tag_names']['buckets'] as $tag) {
                if ($lowerText !== mb_substr(mb_strtolower($tag['key']), 0, $strlen)) {
                    continue;
                }
                $tags[] = ['value' => $tag['key'], 'text' => $tag['key']];
            }
        }

        return $tags;
    }

    /**
     * Drop all documents
     */
    public function dropIndex()
    {
        try {
            $this->elastic->indices()->delete(['index' => $this->getIndexName()]);
        } catch(Missing404Exception $e) {


        }
    }

    public function ensureIndex()
    {
        $params = [
            'index' => $this->getIndexName(),
            'type' => 'documents',
            'body' => []
        ];

        $this->elastic->index($params);

        $params = [
            'index' => $this->getIndexName(),
            'type'  => 'documents',
            'body' => [
                'properties' => [
                    'file' => [
                        'properties' => [
                            '_content' =>[
                                'type' => 'text',
                            ],
                            '_content_type' => [
                                'type' => 'keyword',
                            ],
                            '_name' => [
                                'type' => 'text',
                            ],
                        ]
                    ],
                    'tags' => [
                        'type' => 'keyword',
                    ],
                    'text' => [
                        'type' => 'text',
                    ],
                ],
            ]
        ];

        $this->elastic->indices()->putMapping($params);
    }
}