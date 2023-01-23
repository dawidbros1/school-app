<?php

namespace App\Command\Seeder;

use App\Entity\Roles;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class InitDatabaseCommand extends Command
{
    protected static $defaultName = 'db:init';
    protected static $defaultDescription = 'The command adds basic data to database';
    private $entityManager;
    private $userRepository;
    private $rolesRepository;
    private $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->rolesRepository = $entityManager->getRepository(Roles::class);

        $this->userPasswordHasher = $userPasswordHasher;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->truncateRolesTable();
        $this->addRoles('ROLE_OWNER', 'Właściciel');
        $this->addRoles('ROLE_ADMIN', 'Administrator');
        $this->addRoles('ROLE_TEACHER', 'Nauczyciel');
        $this->addRoles('ROLE_STUDENT', 'Uczeń', true);

        $this->truncateUserTable();
        $this->addUser("owner@wp.pl", 'owner', ["ROLE_OWNER"], "Pan", "Właściciel");
        $this->addUser("admin@wp.pl", 'owner', ["ROLE_ADMIN"], "Pani", "Administrator", true);

        $io->success('OK');
        return Command::SUCCESS;
    }

    private function addRoles(string $name, string $description, bool $flush = false)
    {
        $roles = new Roles();
        $roles->setName($name);
        $roles->setDescription($description);
        $this->rolesRepository->add($roles, $flush);
    }

    private function truncateRolesTable()
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\Roles')->execute();
        $this->entityManager->getConnection()->exec("ALTER TABLE roles AUTO_INCREMENT = 1");
    }

    private function addUser(string $email, string $password, array $roles, string $firstName, string $lastName, bool $flush = false)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $user->setRoles($roles);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $this->userRepository->add($user, $flush);
    }

    private function truncateUserTable()
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
        $this->entityManager->getConnection()->exec("ALTER TABLE user AUTO_INCREMENT = 1");
    }
}