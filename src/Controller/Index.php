<?php
namespace Brave\ForumAuth\Controller;

use Brave\ForumAuth\Model\CharacterRepository;
use Brave\ForumAuth\SessionHandler;
use Brave\Sso\Basics\EveAuthentication;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class Index
{
    /**
     *
     * @var SessionHandler
     */
    private $sessionHandler;

    /**
     * @var string
     */
    private $serviceName;

    /**
     * @var CharacterRepository
     */
    private $characterRepository;

    public function __construct(ContainerInterface $container)
    {
        $this->sessionHandler = $container->get(SessionHandler::class);
        $this->serviceName = $container->get('settings')['brave.serviceName'];
        $this->characterRepository = $container->get(CharacterRepository::class);
    }

    public function __invoke(ServerRequestInterface $request, Response $response)
    {
        $auth = $this->sessionHandler->get('eveAuth');
        if (! $auth instanceof EveAuthentication) {
            return $response->withRedirect('/login');
        }

        $character = $this->characterRepository->find($auth->getCharacterId());

        $html = file_get_contents(ROOT_DIR.'/html/index.html');
        $html = str_replace(
            [
                '{{loginName}}',
                '{{serviceName}}',
                '{{characterId}}',
                '{{characterName}}',
                '{{username}}',
                '{{password}}',
            ],[
                $auth->getCharacterName(),
                $this->serviceName,
                $character ? $character->getId() : 1,
                $character ? $character->getName() : 'please update from Core',
                $character ? $character->getUsername() : '',
                $character ? $character->getPassword() : '',
            ],
            $html
        );

        $response->write($html);
    }
}
