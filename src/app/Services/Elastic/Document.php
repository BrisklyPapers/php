<?php

namespace App\Services\Elastic;

use App\Models\Elastic\Attachment;
use Elasticsearch\Client;

class Document
{
    const INDEX = 'brisklypapers';
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

    /**
     * @param Attachment $document
     * @param array $tags
     *
     * @return array
     */
    public function storeDocument(Attachment $document, array $tags = [])
    {
        $params = [
            'index' => self::INDEX,
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
            'index' => self::INDEX,
            'type' => self::TYPE,
            'id' => $id,
        ];

        $response = $this->elastic->get($params);

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
            'index' => self::INDEX,
            'type' => self::TYPE,
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            'multi_match' => [
                                'query' => $text,
                                'type' => 'best_fields',
                                'fuzziness' => 'AUTO',
                                'fields' => [self::FIELD_TAGS, self::FIELD_TEXT]
                            ]
                        ]
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
        $attachment->tags = $response['_source']['tags'];
        $attachment->id = isset($response['_id']) ? $response['_id'] : null;

        return $attachment;
    }
}