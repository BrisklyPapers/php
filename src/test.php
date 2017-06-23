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
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id',
    'body' => ['testField' => 'abc']
];

$response = $client->index($params);
print_r($response);

$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];

$response = $client->get($params);
print_r($response);

$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'body' => [
        'query' => [
            'match' => [
                'testField' => 'abc'
            ]
        ]
    ]
];

$response = $client->search($params);
print_r($response);

