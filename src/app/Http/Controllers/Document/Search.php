<?php

namespace App\Http\Controllers\Document;

use App\Services\Elastic\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Response;
use Psy\Util\Json;

class Search extends Controller
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
        $documents = $this->elasticDoc->search($text);

        $data = array();

        foreach ($documents as $document) {
            $data[] = [
                'fileName' => $document->fileName,
                'url' => $this->url->route('download-document', ['id' => $document->id]),
                'text' => $this->getAbstract($document->text),
                'tags' => $document->tags
            ];
        }

        return new JsonResponse($data);
    }

    private function getAbstract($text)
    {
        $words = preg_split('/\b/', $text);
        array_filter($words);

        $result = '';
        $len = 0;
        $count = count($words);

        while($len < 100 && $count) {
            $word = array_shift($words);
            $result .= ' ' . $word;
            --$count;
            $len += strlen($word);
        }

        return ltrim($result);
    }
}