<?php

namespace App\Command;

use App\DataFixtures\Factory\UserFactory;
use App\Entity\SchoolClass\SchoolClassStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

# php bin/console db:init
class InitDatabaseCommand extends Command
{
    protected static $defaultName = 'db:init';
    protected static $defaultDescription = 'The command adds basic data to database';
    private $entityManager;
    private $userFactory;

    public function __construct(EntityManagerInterface $entityManager, UserFactory $userFactory)
    {
        $this->entityManager = $entityManager;
        $this->userFactory = $userFactory;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->initUsers();
        $this->initSchoolClassStatuses();

        $io->success('OK');
        return Command::SUCCESS;
    }

    private function initUsers()
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\UserType\Owner')->execute();
        $this->entityManager->getConnection()->exec("ALTER TABLE owner AUTO_INCREMENT = 1");

        $this->userFactory->create([
            'email' => "owner@wp.pl",
            'roles' => 'owner',
            'password' => "owner"
        ], true);
    }

    private function initSchoolClassStatuses()
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\SchoolClass\SchoolClassStatus')->execute();
        $this->entityManager->getConnection()->exec("ALTER TABLE school_class_status AUTO_INCREMENT = 1");

        $repository = $this->entityManager->getRepository(SchoolClassStatus::class);
        $repository->add((new SchoolClassStatus())->setName(SchoolClassStatus::ACTIVE));
        $repository->add((new SchoolClassStatus())->setName(SchoolClassStatus::ARCHIVED), true);
    }
}