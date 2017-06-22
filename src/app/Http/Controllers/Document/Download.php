<?php

namespace App\Http\Controllers\Document;

use App\Services\Elastic\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

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
     * @param $id
     * @return JsonResponse
     */
    public function download($id)
    {
        $document = $this->elasticDoc->loadById($id);

        return Response::make($document->content, 200, [
            'Content-Type' => $document->contentType,
            'Content-Disposition' => 'inline; filename="'.$document->fileName.'"'
        ]);
    }
}