<?php

namespace App\Command;

use App\Entity\UserType\Admin;
use App\Entity\UserType\Student;
use App\Entity\UserType\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

# php bin/console db:user:activate
class UserActivateCommand extends Command
{
   protected static $defaultName = 'db:user:activate';
   protected static $defaultDescription = 'The command adds basic data to database';
   private $em;
   private $userPasswordHasher;

   public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
   {
      $this->em = $entityManager;
      $this->userPasswordHasher = $userPasswordHasher;

      parent::__construct();
   }

   protected function execute(InputInterface $input, OutputInterface $output): int
   {
      $io = new SymfonyStyle($input, $output);

      $this->activate(Admin::class);
      $this->activate(Teacher::class);
      $this->activate(Student::class);

      $this->em->flush();

      $io->success('Users accounts has been activate');
      return Command::SUCCESS;
   }

   private function activate(string $entity)
   {
      $repository = $this->em->getRepository($entity);

      $query = $repository->createQueryBuilder('u')
         ->update()
         ->set('u.code', ':code')
         ->set('u.password', ':password')
         ->where('u.code IS NOT NULL')
         ->setParameter('code', null)
         ->setParameter('password', $this->userPasswordHasher->hashPassword(new $entity(), 'password'))
         ->getQuery();
      $query->execute();
   }
}