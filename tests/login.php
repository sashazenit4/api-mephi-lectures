<?php
require_once __DIR__ . '/../vendor/autoload.php';

$client = new \GuzzleHttp\Client();
$response = $client->post('https://a-holin.ru/login', [
    'form_params' => [
        'login' => 'admin',
        'password' => '',
    ],
]);
$body = $response->getBody()->getContents();
