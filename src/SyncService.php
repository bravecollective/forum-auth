<?php
namespace Brave\ForumAuth;

use Brave\ForumAuth\Model\Character;
use Brave\ForumAuth\Model\CharacterRepository;
use Brave\ForumAuth\Model\Group;
use Brave\NeucoreApi\Api\ApplicationApi;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SyncService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CharacterRepository
     */
    private $characterRepository;

    /**
     * @var ApplicationApi
     */
    private $apiInstance;

    /**
     * @var PhpBB
     */
    private $phpBB;

    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        CharacterRepository $characterRepository,
        ApplicationApi $apiInstance,
        PhpBB $phpBB
    )
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->characterRepository = $characterRepository;
        $this->apiInstance = $apiInstance;
        $this->phpBB = $phpBB;
    }

    /**
     *
     * @param int $characterId
     * @return string[]
     */
    public function getCoreGroups($characterId)
    {
        try {
            $groups = $this->apiInstance->groupsV1($characterId);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return [];
        }

        $groupNames = [];
        foreach ($groups as $group) {
            $groupNames[] = $group->getName();
        }

        return $groupNames;
    }

    public function fetchUpdateCorpAlliance(Character $character)
    {
        $esiHost = 'https://esi.evetech.net';
        $dataSource = 'datasource=tranquility';

        // get corp and alli IDs
        $char = file_get_contents($esiHost.'/latest/characters/'.$character->getId().'/?'.$dataSource);
        $charJson = json_decode($char, true); // $char may be false from file_get_contents()
        if ($charJson === null || $charJson === false) {
            return;
        }

        // corp name
        $corp = file_get_contents($esiHost.'/latest/corporations/'.$charJson['corporation_id'].'/?'.$dataSource);
        $corpJson = json_decode($corp); // $corp may be false from file_get_contents()
        if ($corpJson === null || $corpJson === false) {
            return;
        }
        $character->setCorporationName($corpJson->name);

        // alli name
        if (isset($charJson['alliance_id'])) {
            $alli = file_get_contents($esiHost.'/latest/alliances/'.$charJson['alliance_id'].'/?'.$dataSource);
            $alliJson = json_decode($alli); // $alli may be false from file_get_contents()
            if ($alliJson === null || $alliJson === false) {
                return;
            }
            $character->setAllianceName($alliJson->name);
        } else {
            $character->setAllianceName('');
        }
    }

    /**
     * Add and/or remove groups to/from character.
     *
     * Creates new Group entities if needed and persists them.
     * Does not flush the entity manager.
     *
     * @param Character $character
     * @param array $groupNames Groups that the character should have.
     * @return void
     */
    public function addRemoveGroups(Character $character, array $groupNames)
    {
        $hasGroups = $character->getGroupNames();
        $removeGroups = array_diff($hasGroups, $groupNames);
        $addGroups = array_diff($groupNames, $hasGroups);

        foreach ($removeGroups as $removeGroupName) {
            $removeGroup = $character->getGroupByName($removeGroupName);
            $character->removeGroup($removeGroup);
            $this->entityManager->remove($removeGroup);
        }

        foreach ($addGroups as $addGroupName) {
            $addGroup = new Group();
            $addGroup->setName($addGroupName);
            $addGroup->setCharacter($character);
            $character->addGroup($addGroup);
            $this->entityManager->persist($addGroup);
        }

        $character->setLastUpdate(new \DateTime());
    }

    /**
     * Updates a forum user, creates it if necessary.
     *
     * @param Character $character
     * @param string $ipAddress only used for new accounts
     * @return boolean
     */
    public function updateCreateForumUser(Character $character, $ipAddress = null)
    {
        $userId = $this->phpBB->brave_bb_user_name_to_id($character->getUsername());

        if ($userId === false) {
            $userId = $this->phpBB->brave_bb_account_create(
                $character->getId(),
                $character->getUsername(),
                $ipAddress
            );
        }

        if ($userId === false) {
            return false;
        }

        $this->phpBB->brave_bb_account_update($userId, [
            'corporation_name' => $character->getCorporationName(),
            'alliance_name' => $character->getAllianceName(),
            'core_tags' => implode(',', $character->getGroupNames())
        ]);

        return true;
    }
}
