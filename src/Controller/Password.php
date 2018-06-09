<?php
namespace Brave\ForumAuth\Controller;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class Password
{

    public function __construct(ContainerInterface $container)
    {
    }

    public function reset(ServerRequestInterface $request, Response $response)
    {

        return $response->withRedirect('/?pw-success=0');
    }
}
