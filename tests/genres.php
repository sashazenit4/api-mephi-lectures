<?php
require_once __DIR__ . '/../vendor/autoload.php';

$client = new \GuzzleHttp\Client();
$response = $client->get('https://a-holin.ru/genres', [
    'query' => [
        'top' => 'true',
    ],
]);
$body = $response->getBody()->getContents();
