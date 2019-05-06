<?php
namespace Brave\ForumAuth\Controller;

use Brave\ForumAuth\SessionHandler;
use Brave\Sso\Basics\AuthenticationController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class Authentication extends AuthenticationController
{
    /**
     * @var SessionHandler
     */
    private $sessionHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->sessionHandler = $container->get(SessionHandler::class);
        $this->logger = $container->get(LoggerInterface::class);
    }

    public function auth(ServerRequestInterface $request, ResponseInterface $response)
    {
        try {
            parent::auth($request, $response);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $response->withRedirect('/');
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->sessionHandler->delete('eveAuth');

        return $response->withRedirect('/');
    }
}
