<?php

namespace App\Http\Controllers\Document;

use App\Models\Elastic\Attachment;
use App\Services\Elastic\Document;
use App\User;
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
     * UploadController constructor.
     * @param Document $elasticDoc
     * @param UrlGenerator $url
     */
    public function __construct(Document $elasticDoc, UrlGenerator $url)
    {
        $this->elasticDoc = $elasticDoc;
        $this->url = $url;
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
        $data = array();

        $tags = $request->request->has('tags')
            ? $request->request->get('tags')
            : [];

        /** @var UploadedFile $file */
        foreach ($request->files->all() as $files) {
            foreach ($files as $file) {
                $result = $this->elasticDoc->storeDocument(
                    $this->createAttachment($file),
                    array_values($tags)
                );

                if ($result['_shards']['successful']) {
                    $data[] = ['fileName' => $file->getClientOriginalName(), 'url' => $this->url->route('download-document', ['id' => $result['_id']])];
                } else {
                    $response->setStatusCode(400);
                    $data[] = ['fileName' => $file->getClientOriginalName()];
                }
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

    private function toText(UploadedFile $file)
    {
        return shell_exec(config('ocr.ocr_pdf') . " " . $file->getRealPath() . " deu"); // eng : english, deu: german
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
        $doc->text = $this->toText($file);

        return $doc;
    }
}