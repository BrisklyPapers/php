<?php

require 'vendor/autoload.php';

use Elasticsearch\ClientBuilder;

$hosts = [
    [
        'host' => 'elasticsearch1',
        'port' => '9200',
        'scheme' => 'http',
        'user' => 'elastic',
        'pass' => 'changeme'
    ],
];

$client = ClientBuilder::create()
    ->setHosts($hosts)
    ->setSerializer('\Elasticsearch\Serializers\SmartSerializer')
    ->build();

$params = [
    'index' => 'swiftlib',
    'type' => 'documents',
    'body' => []
];

$response = $client->index($params);
print_r($response);

$params = [
    'index' => 'swiftlib',
    'type'  => 'documents',
    'body' => [
        'properties' => [
            'file' => [
                'type' => 'attachment'
            ]
        ]
    ]
];

$response = $client->indices()->putMapping($params);
print_r($response);