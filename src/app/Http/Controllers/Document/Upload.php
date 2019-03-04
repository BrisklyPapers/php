<?php

namespace App\Http\Controllers\Document;

use App\Services\Document\Parse;
use App\Services\Document\Store;
use App\Services\Elasticsearch\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Upload extends Controller
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
     * @var UploadService
     */
    private $upload;
    /**
     * @var Parse
     */
    private $parse;

    /**
     * UploadController constructor.
     * @param Document $elasticDoc
     * @param UrlGenerator $url
     * @param Store $store
     * @param Parse $parse
     */
    public function __construct(Document $elasticDoc, UrlGenerator $url, Store $store, Parse $parse)
    {
        $this->elasticDoc = $elasticDoc;
        $this->url = $url;
        $this->upload = $store;
        $this->parse = $parse;
    }

    /**
     * Show the profile for the given user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $response = new JsonResponse();

        $tags = $this->getTags($request);
        $rawFiles = $this->getUploadedFiles($request);

        $data = $this->upload->upload($tags, $rawFiles);

        $storedFiles = array();
        foreach ($data as $file) {
            if (isset($file['_id'])) {
                $storedFiles[] = $file;
            } else {
                $response->setStatusCode(400);
            }
        }

        $this->parse->parse($storedFiles);

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getUploadedFiles(Request $request)
    {
        $rawFiles = array();
        /** @var UploadedFile $file */
        foreach ($request->files->all() as $files) {
            foreach ($files as $file) {
                $rawFiles[] = $file;
            }
        }
        return $rawFiles;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getTags(Request $request)
    {
        $tags = $request->request->has('tags')
            ? (array)$request->request->get('tags')
            : [];

        return $tags;
    }

}