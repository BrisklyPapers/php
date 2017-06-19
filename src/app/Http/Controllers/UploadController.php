<?php

namespace App\Http\Controllers;

use App\Models\Elastic\Attachment;
use App\Services\Elastic\Document;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadController extends Controller
{
    /**
     * @var Document
     */
    private $elasticDoc;

    /**
     * UploadController constructor.
     * @param Document $elasticDoc
     */
    public function __construct(Document $elasticDoc)
    {
        $this->elasticDoc = $elasticDoc;
    }

    /**
     * Show the profile for the given user.
     *
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request)
    {
        $response = new JsonResponse();
        $data = array();

        $tags = $request->request->has('tags')
            ? $request->request->get('tags')
            : [];

        /** @var UploadedFile $file */
        foreach ($request->files->all() as $files) {
            foreach ($files as $file) {
                $data['result'] = $this->elasticDoc->storeDocument(
                    $this->createAttachment($file),
                    $tags
                );
            }
        }

        return $response->setData($data);
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

    /**
     * @param UploadedFile $file
     * @return Attachment
     */
    private function createAttachment(UploadedFile $file)
    {
        $doc = new Attachment();
        $doc->fileName = $file->getClientOriginalName();
        $doc->content = $file->getClientMimeType();
        $doc->content = $this->getContents($file);
        return $doc;
    }
}