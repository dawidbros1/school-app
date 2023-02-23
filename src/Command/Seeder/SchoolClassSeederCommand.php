<?php

namespace App\Command\Seeder;

use App\DataFixtures\Factory\SchoolClassFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputOption;

# php bin/console db:seed:schoolClass --limit 5

class SchoolClassSeederCommand extends Command
{
   protected static $defaultName = 'db:seed:schoolClass';
   protected static $defaultDescription = 'The command add classes to database';
   private $factory;

   public function __construct(SchoolClassFactory $factory)
   {
      $this->factory = $factory;

      parent::__construct();
   }

   protected function configure(): void
   {
      $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Option description', 10);
   }

   protected function execute(InputInterface $input, OutputInterface $output): int
   {
      $io = new SymfonyStyle($input, $output);
      $limit = $input->getOption('limit');

      if (!is_numeric($limit)) {
         $io->error("Limit must be type (int), \"" . $limit . "\" given.");
         return Command::SUCCESS;
      }

      for ($i = 1; $i <= $limit; $i++) {
         $this->factory->create([], $i == $limit);
      }

      $io->success($limit . ' classes has been added');

      return Command::SUCCESS;
   }
}