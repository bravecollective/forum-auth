<?php
use Brave\ForumAuth\Controller\Authentication;
use Brave\ForumAuth\Controller\Core;
use Brave\ForumAuth\Controller\Index;
use Brave\ForumAuth\Controller\Password;
use Brave\Sso\Basics\AuthenticationController;

return function (\Psr\Container\ContainerInterface $container) {
    /** @var \Slim\App $app */
    $app = $container[\Slim\App::class];

    // SSO via sso-basics package
    $app->get('/login',  AuthenticationController::class . ':index');
    $app->get('/auth',   Authentication::class . ':auth');
    $app->get('/logout', Authentication::class . ':logout');

    // app routes
    $app->get('/',            Index::class);
    $app->get('/core-update', Core::class . ':update');
    $app->get('/pw-reset',    Password::class . ':reset');

    return $app;
};
