<?php

namespace App\Command;

use App\DataFixtures\Factory\RolesFactory;
use App\DataFixtures\Factory\UserFactory;
use App\Entity\Roles;
use App\Entity\User;
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
    private $userFactory, $rolesFactory;

    public function __construct(EntityManagerInterface $entityManager, UserFactory $userFactory, RolesFactory $rolesFactory)
    {
        $this->entityManager = $entityManager;
        $this->userFactory = $userFactory;
        $this->rolesFactory = $rolesFactory;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->truncateRolesTable();
        $this->rolesFactory->create(['name' => "ROLE_OWNER", 'description' => "Właściciel"]);
        $this->rolesFactory->create(['name' => "ROLE_ADMIN", 'description' => "Administrator"]);
        $this->rolesFactory->create(['name' => "ROLE_TEACHER", 'description' => "Nauczyciel"]);
        $this->rolesFactory->create(['name' => "ROLE_STUDENT", 'description' => "Uczeń"], true);

        $this->truncateUserTable();
        $this->userFactory->create([
            'email' => "owner@wp.pl",
            'roles' => ["ROLE_OWNER"],
            'password' => "owner"
        ], true);

        $io->success('OK');
        return Command::SUCCESS;
    }

    private function truncateRolesTable()
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\Roles')->execute();
        $this->entityManager->getConnection()->exec("ALTER TABLE roles AUTO_INCREMENT = 1");
    }

    private function truncateUserTable()
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
        $this->entityManager->getConnection()->exec("ALTER TABLE user AUTO_INCREMENT = 1");
    }
}