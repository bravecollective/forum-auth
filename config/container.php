<?php

use Brave\ForumAuth\ErrorHandler;
use Brave\ForumAuth\Model\Character;
use Brave\ForumAuth\Model\CharacterRepository;
use Brave\ForumAuth\PhpBB;
use Brave\ForumAuth\SessionHandler;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return [
    'settings' => require_once 'config.php',

    \Slim\App::class => function (ContainerInterface $container) {
        return new Slim\App($container);
    },

    \League\OAuth2\Client\Provider\GenericProvider::class => function (ContainerInterface $container) {
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

    \Brave\Sso\Basics\AuthenticationProvider::class => function (ContainerInterface $container) {
        $settings = $container->get('settings');

        return new \Brave\Sso\Basics\AuthenticationProvider(
            $container->get(\League\OAuth2\Client\Provider\GenericProvider::class),
            explode(' ', $settings['SSO_SCOPES'])
        );
    },

    SessionHandler::class => function (ContainerInterface $container) {
        return new SessionHandler($container);
    },

    \Brave\Sso\Basics\SessionHandlerInterface::class => function (ContainerInterface $container) {
        return $container->get(SessionHandler::class);
    },

    LoggerInterface::class => function (ContainerInterface $container) {
        $logger = new \Monolog\Logger('errors');
        $logger->pushHandler(new \Monolog\Handler\StreamHandler(BRAVE_ROOT_DIR.'/logs/error.log', \Monolog\Logger::DEBUG));

        return $logger;
    },

    'errorHandler' => function (ContainerInterface $container) {
        return new ErrorHandler(
            $container->get('settings')['displayErrorDetails'],
            $container->get(LoggerInterface::class)
        );
    },

    \Doctrine\ORM\EntityManagerInterface::class => function (ContainerInterface $container) {
        // just use dev mode, so this needs not cache
        $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration([BRAVE_ROOT_DIR . '/src'], true);

        return \Doctrine\ORM\EntityManager::create(['url' => $container->get('settings')['DB_URL']], $config);
    },

    CharacterRepository::class => function (ContainerInterface $container) {
        $em = $container->get(\Doctrine\ORM\EntityManagerInterface::class);
        $class = $em->getMetadataFactory()->getMetadataFor(Character::class);

        return new CharacterRepository($em, $class);
    },

    \Brave\NeucoreApi\Api\ApplicationApi::class => function (ContainerInterface $container) {
        $apiKey = base64_encode(
            $container->get('settings')['CORE_APP_ID'] .
            ':'.
            $container->get('settings')['CORE_APP_TOKEN']
        );
        $config = Brave\NeucoreApi\Configuration::getDefaultConfiguration();
        $config->setHost($container->get('settings')['CORE_URL']);
        $config->setApiKey('Authorization', $apiKey);
        $config->setApiKeyPrefix('Authorization', 'Bearer');

        return new Brave\NeucoreApi\Api\ApplicationApi(null, $config);
    },

    PhpBB::class => function (ContainerInterface $container) {
        $cfg_bb_groups = $container->get('settings')['cfg_bb_groups'];

        return new PhpBB($cfg_bb_groups);
    }
];
