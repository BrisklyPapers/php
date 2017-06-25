<?php

namespace App\Http\Controllers;

use App\Services\Elastic\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Response;
use Psy\Util\Json;

class Tags extends Controller
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
    public function search(Request $request)
    {
        $text = $request->get('q', '');

        if (empty($text)) {
            return new JsonResponse();
        }


        $data = $this->elasticDoc->searchTags($text);

        array_unshift($data, ['value' => $text, 'text' => $text]);

        return new JsonResponse($data);
    }
}