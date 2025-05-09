<?php

use Bitrix\Main\Routing\RoutingConfigurator;
use Ksusha\Main\Controller\Auth as AuthController;
use Ksusha\Main\Controller\User as UserController;
use Bitrix\Main\Loader;

Loader::includeModule('ksusha.main');

return function (RoutingConfigurator $routes) {
    $routes->post('/login', [AuthController::class, 'login']);
    $routes->get('/user', [UserController::class, 'getUser']);
};
