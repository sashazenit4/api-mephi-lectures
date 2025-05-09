<?php
use Bitrix\Main\Routing\RoutingConfigurator;
use Ksusha\Main\Controller\Auth as AuthController;
use Bitrix\Main\Loader;

Loader::includeModule('ksusha.main');

return function (RoutingConfigurator $routes) {
    $routes->post('/login', function() {
        $authController = new AuthController();
        return $authController->loginAction();
    });
};
