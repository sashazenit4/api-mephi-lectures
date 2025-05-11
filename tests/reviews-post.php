<?php
require_once __DIR__ . '/../vendor/autoload.php';

$client = new \GuzzleHttp\Client();
$response = $client->post('https://a-holin.ru/movies/33/reviews', [
    'headers' => [
        'Authorization' => 'Bearer ',
    ],
    'form_params' => [
        'title'   => 'Триумфальное завершение космической оперы',
        'rating'  => '9.0',
        'content' => '«Дюна: Часть вторая» превосходит первый фильм во всех отношениях...',
        'pros'    => [
            'Блестящая актерская игра всего состава',
            'Глубокое философское содержание',
            'Идеальный баланс экшена и драмы',
        ],
        'cons'    => [
            'Требует обязательного просмотра первой части',
            'Слишком много персонажей для одного фильма',
        ],
        'quote'   => '«Это новый «Властелин Колец» для поколения Z» - The Guardian',
    ],
]);
$body = $response->getBody()->getContents();
