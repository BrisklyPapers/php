<?php

namespace App\Http\Controllers\Document;

use App\Models\Elastic\Attachment;
use App\Services\Elastic\Document;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Download extends Controller
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
     * @return JsonResponse
     */
    public function download($id)
    {
        return "docu $id";
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
        return shell_exec(config('ocr.ocr_pdf') . " " . $file->getRealPath() . " de");
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