<?php

namespace App\Providers;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function (Application $app) {

            $hosts = [
                [
                    'host' => config('elasticsearch.host'),
                    'port' => config('elasticsearch.port'),
                    'scheme' => config('elasticsearch.scheme'),
                    'user' => config('elasticsearch.user'),
                    'pass' => config('elasticsearch.password')
                ],
            ];

            return ClientBuilder::create()
                ->setHosts($hosts)
                ->setSerializer('\Elasticsearch\Serializers\SmartSerializer')
                ->build();
        });
    }
}
