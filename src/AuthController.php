<?php
namespace Brave\CoreConnector;

use Brave\Sso\Basics\AuthenticationController;
use Brave\Sso\Basics\SessionHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;

class AuthController extends AuthenticationController
{

    /**
     * @var SessionHandler
     */
    private $sessionHandler;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->sessionHandler = $container->get(SessionHandlerInterface::class);
    }

    public function auth(ServerRequestInterface $request, ResponseInterface $response, $arguments)
    {
        try {
            parent::auth($request, $response, $arguments);
        } catch (\Exception $e) {}

        return $response->withHeader('Location', '/');
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->sessionHandler->delete('eveAuth');

        return $response->withHeader('Location', '/');
    }
}
