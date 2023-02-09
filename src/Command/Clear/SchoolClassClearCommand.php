<?php

namespace App\Command\Clear;

use App\Entity\SchoolClass\SchoolClass;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

# php bin/console db:clear:class

class SchoolClassClearCommand extends Command
{
   protected static $defaultName = 'db:clear:class';
   protected static $defaultDescription = 'The command remove school classes from database';
   private $em;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;

      parent::__construct();
   }

   protected function execute(InputInterface $input, OutputInterface $output): int
   {
      $io = new SymfonyStyle($input, $output);

      if ($classes = $this->em->getRepository(SchoolClass::class)->findAll()) {
         foreach ($classes as $class) {
            $this->em->remove($class);
         }

         $this->em->flush();
      }

      $this->em->getConnection()->exec("ALTER TABLE school_class AUTO_INCREMENT = 1");

      $io->success('school clases cleared');

      return Command::SUCCESS;
   }
}