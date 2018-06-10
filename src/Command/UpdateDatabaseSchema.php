<?php
namespace Brave\ForumAuth\Command;

use Brave\ForumAuth\Model\Character;
use Brave\ForumAuth\Model\Group;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Tools\SchemaTool;

class UpdateDatabaseSchema extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->entityManager = $container->get(EntityManagerInterface::class);
    }

    protected function configure()
    {
        $this
            ->setName('db:schema-update')
            ->setDescription('Creates or updates the database schema based on the entity classes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $classes = [
            $this->entityManager->getClassMetadata(Character::class),
            $this->entityManager->getClassMetadata(Group::class)
        ];

        $tool = new SchemaTool($this->entityManager);
        $tool->updateSchema($classes);

        $output->writeln('Schema updated.');
    }
}
