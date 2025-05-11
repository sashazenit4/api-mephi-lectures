<?php
require_once __DIR__ . '/../vendor/autoload.php';

$client = new \GuzzleHttp\Client();
$response = $client->get('https://a-holin.ru/movies/33');
$body = $response->getBody()->getContents();
