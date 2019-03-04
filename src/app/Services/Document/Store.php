<?php

namespace App\Services\Document;

use App\Models\Elasticsearch\Attachment;
use App\Services\Elasticsearch\Document;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Store
{
    /**
     * @var Document
     */
    private $elasticDoc;
    /**
     * @var UrlGenerator
     */
    private $url;

    /**
     * @param Document $elasticDoc
     * @param UrlGenerator $url
     */
    public function __construct(Document $elasticDoc, UrlGenerator $url)
    {
        $this->elasticDoc = $elasticDoc;
        $this->url = $url;
    }

    /**
     * @param array $tags
     * @param UploadedFile[] $files
     *
     * @return array
     */
    public function upload(array $tags, array $files)
    {
        $data = array();

        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $result = $this->elasticDoc->storeDocument(
                $this->createAttachment($file),
                array_values($tags)
            );

            if (isset($result['_shards']['successful'])) {
                $data[] = [
                    'fileName' => $file->getClientOriginalName(),
                    'url' => $this->url->route('download-document', ['id' => $result['_id']]),
                    '_id' => $result['_id']
                ];
            } else {
                $data[] = [
                    'fileName' => $file->getClientOriginalName()
                ];
            }
        }

        return $data;
    }

    /**
     * @param UploadedFile $file
     * @return Attachment
     */
    private function createAttachment(UploadedFile $file)
    {
        $doc = new Attachment();
        $doc->fileName = $file->getClientOriginalName();
        $doc->contentType = $file->getClientMimeType();
        $doc->content = $this->getContents($file);

        return $doc;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function getContents(UploadedFile $file)
    {
        $fh = fopen($file->getRealPath(), 'r');
        $bin = fread($fh, $file->getSize());
        fclose($fh);

        return $bin;
    }
}