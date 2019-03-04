<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Elasticsearch\ClientBuilder;

class InitElasticsearch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TODOO: use a proper connection
        $hosts = [
            [
                'host' => config('elasticsearch.host'),
                'port' => config('elasticsearch.port'),
                'scheme' => config('elasticsearch.scheme'),
                'user' => config('elasticsearch.user'),
                'pass' => config('elasticsearch.password')
            ],
        ];

        $client = ClientBuilder::create()
            ->setHosts($hosts)
            ->setSerializer('\Elasticsearch\Serializers\SmartSerializer')
            ->build();

        $params = [
            'index' => 'brisklypapers' . config('elasticsearch.index_prefix'),
            'type' => 'documents',
            'body' => []
        ];

        $client->index($params);

        $params = [
            'index' => 'brisklypapers' . config('elasticsearch.index_prefix'),
            'type'  => 'documents',
            'body' => [
                'properties' => [
                    'file' => [
                        'properties' => [
                            '_content' =>[
                                'type' => 'text',
                            ],
                            '_content_type' => [
                                'type' => 'keyword',
                            ],
                            '_name' => [
                                'type' => 'text',
                            ],
                        ]
                    ],
                    'tags' => [
                        'type' => 'keyword',
                    ],
                    'text' => [
                        'type' => 'text',
                    ],
                ],
            ]
        ];

        $client->indices()->putMapping($params);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
