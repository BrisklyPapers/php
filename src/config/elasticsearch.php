<?php

return [
    'scheme' => env('ELASTIC_SCHEME', 'http'),
    'host' => env('ELASTIC_HOST', 'elasticsearch1'),
    'user' => env('ELASTIC_USER', 'elastic'),
    'password' => env('ELASTIC_PASSWORD', 'changeme'),
    'port' => env('ELASTIC_PORT', 9200),
];
