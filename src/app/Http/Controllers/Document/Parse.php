<?php

namespace App\Http\Controllers\Document;

use App\Models\Elasticsearch\Attachment;
use App\Services\Elasticsearch\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class Parse extends Controller
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
     * @param $id
     * @return JsonResponse
     */
    public function text($id)
    {
        $document = $this->elasticDoc->loadById($id);

        $document->text = $this->toText($document);

        $this->elasticDoc->storeDocument($document);

        return new JsonResponse(['id' => $id, 'text' => $document->text]);
    }

    /**
     * @param Attachment $document
     * @return string
     */
    private function toText(Attachment $document)
    {
        $fileName = tempnam('/tmp', 'doc_');

        file_put_contents($fileName, $document->content);

        $text = shell_exec(config('ocr.ocr_pdf') . " " . $fileName . " deu+eng"); // eng : english, deu: german

        unlink($fileName);

        return $text;
    }
}