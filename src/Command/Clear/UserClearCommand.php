<?php

namespace App\Command\Clear;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

# php bin/console db:clear:users

class UserClearCommand extends Command
{
   protected static $defaultName = 'db:clear:users';
   protected static $defaultDescription = 'The command remove users from database [ exclude owner ]';
   private $em;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;

      parent::__construct();
   }

   protected function execute(InputInterface $input, OutputInterface $output): int
   {
      $io = new SymfonyStyle($input, $output);

      $this->truncateUserTable('Admin', 'admin');
      $this->truncateUserTable('Teacher', 'teacher');
      $this->truncateUserTable('Student', 'student');

      $io->success('users cleared');

      return Command::SUCCESS;
   }

   private function truncateUserTable(string $userType, string $table)
   {
      $this->em->createQuery("DELETE FROM App\Entity\UserType\\$userType")->execute();
      $this->em->getConnection()->exec("ALTER TABLE $table AUTO_INCREMENT = 1");
   }
}