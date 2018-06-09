<?php
namespace Brave\ForumAuth\Controller;

use Brave\ForumAuth\Model\Character;
use Brave\ForumAuth\Model\CharacterRepository;
use Brave\ForumAuth\SessionHandler;
use Brave\NeucoreApi\Api\ApplicationApi;
use Brave\Sso\Basics\EveAuthentication;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Http\Response;
use Brave\ForumAuth\Model\Group;

class Core
{
    /**
     * @var ApplicationApi
     */
    private $apiInstance;

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

    public function __construct(ContainerInterface $container)
    {
        $this->apiInstance = $container->get(ApplicationApi::class);
        $this->sessionHandler = $container->get(SessionHandler::class);
        $this->logger = $container->get(LoggerInterface::class);
        $this->characterRepository = $container->get(CharacterRepository::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
    }

    public function update(ServerRequestInterface $request, Response $response)
    {
        // check login
        $auth = $this->sessionHandler->get('eveAuth');
        if (! $auth instanceof EveAuthentication) {
            return $response->withRedirect('/login');
        }

        // get Core groups
        if (($groupNames = $this->getCoreGroups($auth)) === null) {
            return $response->withRedirect('/?core-success=0');
        }

        // update or create local character
        $character = $this->updateCreateCharacter($auth);

        // add and remove groups
        $this->addRemoveGroups($character, $groupNames);

        // save
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        return $response->withRedirect('/?core-success=1');
    }

    /**
     *
     * @param EveAuthentication $auth
     * @return null|string[]
     */
    private function getCoreGroups(EveAuthentication $auth)
    {
        try {
            $groups = $this->apiInstance->groupsV1($auth->getCharacterId());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return;
        }

        $groupNames = [];
        foreach ($groups as $group) {
            $groupNames[] = $group->getName();
        }

        return $groupNames;
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

    private function addRemoveGroups(Character $character, array $groupNames)
    {
        $hasGroups = array_intersect($character->getGroupNames(), $groupNames);
        $removeGroups = array_diff($hasGroups, $groupNames);
        $addGroups = array_diff($groupNames, $hasGroups);

        foreach ($removeGroups as $removeGroupName) {
            $character->removeGroupByName($removeGroupName);
        }

        foreach ($addGroups as $addGroupName) {
            $addGroup = new Group();
            $addGroup->setName($addGroupName);
            $addGroup->setCharacter($character);
            $this->entityManager->persist($addGroup);
        }
    }
}
