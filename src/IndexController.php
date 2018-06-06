<?php
namespace Brave\CoreConnector;

use Brave\Sso\Basics\EveAuthentication;
use Brave\Sso\Basics\SessionHandlerInterface;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class IndexController
{

    /**
     *
     * @var SessionHandlerInterface
     */
    private $sessionHandler;

    public function __construct(ContainerInterface $container)
    {
        $this->sessionHandler = $container->get(SessionHandlerInterface::class);
    }

    public function __invoke(ServerRequestInterface $request, Response $response)
    {
        $auth = $this->sessionHandler->get('eveAuth');
        if ($auth instanceof EveAuthentication) {
            $response->write(
                'Hello ' . $auth->getCharacterName() . '<br>' .
                '<a href="/logout">logout</a>'
            );
        } else {
            return $response->withRedirect('/login');
        }
    }
}
