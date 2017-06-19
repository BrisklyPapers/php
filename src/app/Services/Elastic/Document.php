<?php

namespace App\Services\Elastic;

use App\Models\Elastic\Attachment;
use Elasticsearch\Client;

class Document
{
    const INDEX = 'swiftlib';
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
}