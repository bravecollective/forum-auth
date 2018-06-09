<?php
namespace Brave\ForumAuth\Controller;

use Brave\ForumAuth\Model\CharacterRepository;
use Brave\ForumAuth\SessionHandler;
use Brave\Sso\Basics\EveAuthentication;
use Interop\Container\ContainerInterface;
use Slim\Http\Request;
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

    public function __invoke(Request $request, Response $response)
    {
        $auth = $this->sessionHandler->get('eveAuth');
        if (! $auth instanceof EveAuthentication) {
            return $response->withRedirect('/login');
        }

        // alerts
        $alertType = '';
        $alertText = '';
        if ($request->getParam('core-success') === '0') {
            $alertType = 'warning';
            $alertText = 'Failed to update character from Core.';
        } elseif ($request->getParam('core-success') === '1') {
            $alertType = 'success';
            $alertText = 'Update completed.';
        }
        if ($request->getParam('pw-success') === '0') {
            $alertType = 'warning';
            $alertText = 'Failed to update password.';
        }

        // get character from local database - may not yet exist
        $character = $this->characterRepository->find($auth->getCharacterId());

        $html = file_get_contents(BRAVE_ROOT_DIR.'/html/index.html');
        $html = str_replace(
            [
                '{{loginName}}',
                '{{serviceName}}',
                '{{characterId}}',
                '{{characterName}}',
                '{{username}}',
                '{{password}}',
                '{{alertType}}',
                '{{alertText}}',
            ],[
                $auth->getCharacterName(),
                $this->serviceName,
                $character ? $character->getId() : 1,
                $character ? $character->getName() : 'please update from Core',
                $character ? $character->getUsername() : '',
                $character ? $character->getPassword() : '',
                $alertType,
                $alertText,
            ],
            $html
        );

        $response->write($html);
    }
}
