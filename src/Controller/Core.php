<?php
namespace Brave\ForumAuth\Controller;

use Brave\ForumAuth\Model\Character;
use Brave\ForumAuth\Model\CharacterRepository;
use Brave\ForumAuth\SessionHandler;
use Brave\ForumAuth\SyncService;
use Brave\Sso\Basics\EveAuthentication;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Http\Response;

class Core
{
    /**
     * @var SessionHandler
     */
    private $sessionHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CharacterRepository
     */
    private $characterRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SyncService
     */
    private $syncService;

    public function __construct(ContainerInterface $container)
    {
        $this->sessionHandler = $container->get(SessionHandler::class);
        $this->logger = $container->get(LoggerInterface::class);
        $this->characterRepository = $container->get(CharacterRepository::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->syncService = $container->get(SyncService::class);
    }

    public function update(ServerRequestInterface $request, Response $response)
    {
        // check login
        $auth = $this->sessionHandler->get('eveAuth');
        if (! $auth instanceof EveAuthentication) {
            return $response->withRedirect('/login');
        }

        $characterId = $auth->getCharacterId();

        // get Core groups
        $groupNames = $this->syncService->getCoreGroups($characterId);

        // check if there are any groups at all: no Core groups = no forum account
        if (count($groupNames) === 0) {
            return $response->withRedirect('/?core-success=0');
        }

        // create and/or update local character
        $character = $this->updateCreateCharacter($auth);
        $this->syncService->fetchUpdateCorpAlliance($character);

        // add and remove groups from local character
        $this->syncService->addRemoveGroups($character, $groupNames);

        // save
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return $response->withRedirect('/?core-success=0');
        }

        // create/update forum user
        if (! $this->syncService->updateCreateForumUser($character, $request->getAttribute('ip_address'))) {
            return $response->withRedirect('/?core-success=0');
        }

        return $response->withRedirect('/?core-success=1');
    }

    /**
     * @param EveAuthentication $auth
     * @return \Brave\ForumAuth\Model\Character
     */
    private function updateCreateCharacter(EveAuthentication $auth)
    {
        $character = $this->characterRepository->find($auth->getCharacterId());
        if ($character === null) {
            $character = new Character();
            $character->setId($auth->getCharacterId());
            $character->setUsername($auth->getCharacterName()); // never change once set
            $this->entityManager->persist($character);
        }
        $character->setName($auth->getCharacterName()); // EVE character names can change

        return $character;
    }
}
