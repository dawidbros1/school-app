<?php

namespace App\Command\Init;

use App\DataFixtures\Factory\SchoolSubjectFactory;
use App\Entity\SchoolSubject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

# php bin/console db:init:subject

class SchoolSubjectInitCommand extends Command
{
   protected static $defaultName = 'db:init:subject';
   protected static $defaultDescription = '[INIT] The command add default school subjects';
   private $em;
   private $factory;

   public function __construct(EntityManagerInterface $entityManager, SchoolSubjectFactory $factory)
   {
      $this->em = $entityManager;
      $this->factory = $factory;
      parent::__construct();
   }


   protected function execute(InputInterface $input, OutputInterface $output): int
   {
      $io = new SymfonyStyle($input, $output);

      if (empty($this->em->getRepository(SchoolSubject::class)->findAll())) {

         $subjects = [
            'Matematyka',
            'Język polski',
            'Historia',
            'WOS',
            'Fizyka',
            'Biologia',
            'Wychowanie Fizyczne',
            'Religia',
            'Etyka',
            'Plastyka',
            'Chemia',
            'Muzyka',
            'Plastyka',
            'Geografia',
            'Informatyka',
            'Przyroda',
            'Technika',
            'Język angielski',
            'Język niemiecki'
         ];

         foreach ($subjects as $index => $subject) {
            $this->factory->create($subject, count($subjects) == ($index + 1));
         }

         $io->success("success");
      } else {
         $io->error("Table is not empty");
      }

      return Command::SUCCESS;
   }
}