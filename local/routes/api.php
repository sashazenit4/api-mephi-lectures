<?php

use Bitrix\Main\Routing\RoutingConfigurator;
use Ksusha\Main\Controller\Auth as AuthController;
use Ksusha\Main\Controller\User as UserController;
use Ksusha\Main\Controller\Genres as GenresController;
use Ksusha\Main\Controller\Movies as MoviesController;
use Bitrix\Main\Loader;

Loader::includeModule('ksusha.main');

return function (RoutingConfigurator $routes) {
    $routes->post('/login', [AuthController::class, 'login']);
    $routes->get('/user', [UserController::class, 'getUser']);
    $routes->get('/genres', [GenresController::class, 'getGenres']);
    $routes->get('/movies', [MoviesController::class, 'getMovies']);
    $routes->get('/movies/{movieId}', [MoviesController::class, 'getMovie']);
    $routes->get('/movies/{movieId}/reviews', [MoviesController::class, 'getMovieReviews']);
};
