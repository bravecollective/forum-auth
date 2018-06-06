<?php

use Brave\CoreConnector\AuthController;
use Brave\CoreConnector\IndexController;
use Brave\Sso\Basics\AuthenticationController;

return function (\Psr\Container\ContainerInterface $container)
{
    /** @var \Slim\App $app */
    $app = $container[\Slim\App::class];

    // SSO via sso-basics package
    $app->get('/login', AuthenticationController::class . ':index');
    $app->get('/auth', AuthController::class . ':auth');
    $app->get('/logout', AuthController::class . ':logout');

    // app routes
    $app->get('/', IndexController::class);

    return $app;
};
