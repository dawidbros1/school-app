<?php

namespace App\Command\Init;

use App\DataFixtures\Factory\ClassTimeFactory;
use App\Entity\Schedule\ClassTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

# php bin/console db:init:classTime

class ClassTimeInitCommand extends Command
{
   protected static $defaultName = 'db:init:classTime';
   protected static $defaultDescription = '[INIT] The command add default class times';
   private $em;
   private $factory;

   public function __construct(EntityManagerInterface $entityManager, ClassTimeFactory $factory)
   {
      $this->em = $entityManager;
      $this->factory = $factory;
      parent::__construct();
   }


   protected function execute(InputInterface $input, OutputInterface $output): int
   {
      $io = new SymfonyStyle($input, $output);

      if (empty($this->em->getRepository(ClassTime::class)->findAll())) {
         $this->factory->create("8:00", "8:45");
         $this->factory->create("8:55", "9:40");
         $this->factory->create("9:50", "10:35");
         $this->factory->create("10:45", "11:30");
         $this->factory->create("11:40", "12:25");
         $this->factory->create("12:35", "13:20");
         $this->factory->create("13:30", "14:15");
         $this->factory->create("14:25", "15:10", true);

         $io->success("success");
      } else {
         $io->error("Table is not empty");
      }

      return Command::SUCCESS;
   }
}