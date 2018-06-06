<?php

use Brave\CoreConnector\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    'settings' => require_once('config.php'),

    \Slim\App::class => function (ContainerInterface $container)
    {
        return new Slim\App($container);
    },

    \League\OAuth2\Client\Provider\GenericProvider::class => function (ContainerInterface $container)
    {
        $settings = $container->get('settings');

        return new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $settings['SSO_CLIENT_ID'],
            'clientSecret' => $settings['SSO_CLIENT_SECRET'],
            'redirectUri' => $settings['SSO_REDIRECTURI'],
            'urlAuthorize' => $settings['SSO_URL_AUTHORIZE'],
            'urlAccessToken' => $settings['SSO_URL_ACCESSTOKEN'],
            'urlResourceOwnerDetails' => $settings['SSO_URL_RESOURCEOWNERDETAILS'],
        ]);
    },

    \Brave\Sso\Basics\AuthenticationProvider::class => function (ContainerInterface $container)
    {
        $settings = $container->get('settings');

        return new \Brave\Sso\Basics\AuthenticationProvider(
            $container->get(\League\OAuth2\Client\Provider\GenericProvider::class),
            explode(' ', $settings['SSO_SCOPES'])
        );
    },

    \Brave\CoreConnector\SessionHandler::class => function (ContainerInterface $container) {
        return new \Brave\CoreConnector\SessionHandler($container);
    },

    \Brave\Sso\Basics\SessionHandlerInterface::class => function (ContainerInterface $container) {
        return $container->get(\Brave\CoreConnector\SessionHandler::class);
    },

    LoggerInterface::class => function (ContainerInterface $container) {
        $logger = new Logger('errors');
        $logger->pushHandler(new StreamHandler(__DIR__.'/../logs/error.log', Logger::DEBUG));
        return $logger;
    },

    'errorHandler' => function ($c) {
        return new ErrorHandler(
            $c->get('settings')['displayErrorDetails'],
            $c->get(LoggerInterface::class)
        );
    }
];
