<?php

namespace App\Command\Clear;

use App\Entity\SchoolClass\SchoolClass;
use App\Entity\UserType\Teacher;
use App\Enum\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

# php bin/console db:clear:user

class UserClearCommand extends Command
{
   protected static $defaultName = 'db:clear:user';
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

      $this->clearTeacherTable(); // [ class <--> teacher ]
      $this->truncateUserTable(UserType::ADMIN, 'admin');
      $this->truncateUserTable(UserType::STUDENT, 'student');

      $io->success('users cleared');

      return Command::SUCCESS;
   }

   private function truncateUserTable(string $userType, string $table)
   {
      $userType = ucfirst($userType);

      $this->em->createQuery("DELETE FROM App\Entity\UserType\\$userType")->execute();
      $this->em->getConnection()->exec("ALTER TABLE $table AUTO_INCREMENT = 1");
   }

   private function clearTeacherTable()
   {
      if ($teachers = $this->em->getRepository(Teacher::class)->findAll()) {
         foreach ($teachers as $teacher) {
            $this->em->remove($teacher);
         }
         $this->em->flush();
      }

      $this->em->createQuery("DELETE FROM App\Entity\UserType\Teacher")->execute();
   }
}