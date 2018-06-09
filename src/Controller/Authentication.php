<?php
namespace Brave\ForumAuth\Controller;

use Brave\ForumAuth\SessionHandler;
use Brave\Sso\Basics\AuthenticationController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;
use Slim\Http\Response;

class Authentication extends AuthenticationController
{
    /**
     * @var SessionHandler
     */
    private $sessionHandler;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->sessionHandler = $container->get(SessionHandler::class);
    }

    public function auth(ServerRequestInterface $request, Response $response, $arguments)
    {
        try {
            parent::auth($request, $response, $arguments);
        } catch (\Exception $e) {}

        return $response->withRedirect('/');
    }

    public function logout(ServerRequestInterface $request, Response $response)
    {
        $this->sessionHandler->delete('eveAuth');

        return $response->withRedirect('/');
    }
}
