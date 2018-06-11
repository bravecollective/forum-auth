<?php
namespace Brave\ForumAuth\Command;

use Brave\ForumAuth\Model\CharacterRepository;
use Brave\ForumAuth\SyncService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncGroups extends Command
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
     * @var SyncService
     */
    private $syncService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->logger = $container->get(LoggerInterface::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->characterRepository = $container->get(CharacterRepository::class);
        $this->syncService = $container->get(SyncService::class);
    }

    protected function configure()
    {
        $this
            ->setName('groups:sync')
            ->setDescription('Updates groups from Core, updates groups in Forum, '.
                'activates/deactivates forum users.')
            ->addOption('sleep', 's', InputOption::VALUE_OPTIONAL,
                'Time to sleep in milliseconds after each character update', 200);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sleep = (int) $input->getOption('sleep');

        $charIds = [];
        $chars = $this->characterRepository->findBy([], ['lastUpdate' => 'ASC']);
        foreach ($chars as $char) {
            $charIds[] = $char->getId();
        }

        foreach ($charIds as $charId) {
            $this->entityManager->clear(); // detaches all objects from Doctrine
            usleep($sleep * 1000);

            $character = $this->characterRepository->find($charId);
            $this->syncService->fetchUpdateCorpAlliance($character);
            $groupNames = $this->syncService->getCoreGroups($character->getId());
            $this->syncService->addRemoveGroups($character, $groupNames);

            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                continue;
            }

            if ($this->syncService->updateCreateForumUser($character)) {
                $output->writeln('Updated '. $charId);
            }
        }

        $output->writeln('All done.');
    }
}
