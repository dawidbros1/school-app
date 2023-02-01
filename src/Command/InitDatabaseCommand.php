<?php

namespace App\Command;

use App\DataFixtures\Factory\UserFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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

        $this->truncateUserTable();
        $this->userFactory->create([
            'email' => "owner@wp.pl",
            'roles' => 'owner',
            'password' => "owner"
        ], true);

        $io->success('OK');
        return Command::SUCCESS;
    }

    private function truncateUserTable()
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\UserType\Owner')->execute();
        $this->entityManager->getConnection()->exec("ALTER TABLE owner AUTO_INCREMENT = 1");
    }
}