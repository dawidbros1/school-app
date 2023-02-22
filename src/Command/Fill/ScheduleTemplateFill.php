<?php

namespace App\Command\Fill;

use App\Entity\Schedule\ClassTime;
use App\Entity\Schedule\ScheduleTemplate;
use App\Entity\SchoolClass\SchoolClass;
use App\Entity\SchoolSubject;
use App\Entity\UserType\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

# php bin/console db:fill:scheduleTemplate

class ScheduleTemplateFill extends Command
{
   protected static $defaultName = 'db:fill:scheduleTemplate';
   protected static $defaultDescription = 'The command create schedule template to each class.';
   private $em;
   private $classes;
   private $subjects;
   private $teachers;
   private $classTimes;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
      $this->classes = $this->em->getRepository(SchoolClass::class)->findAll();
      $this->subjects = $this->em->getRepository(SchoolSubject::class)->findAll();
      $this->teachers = $this->em->getRepository(Teacher::class)->findAll();
      $this->classTimes = $this->em->getRepository(ClassTime::class)->findAll();

      parent::__construct();
   }

   protected function execute(InputInterface $input, OutputInterface $output): int
   {
      $io = new SymfonyStyle($input, $output);
      $days = ['monday', "tuesday", 'wednesday', 'thursday', 'friday'];

      if ($this->checkRequirements($io)) {
         foreach ($this->classes as $class) {
            foreach ($days as $day) {
               foreach ($this->classTimes as $classTime) {
                  if (rand(1, 10) > 7) {
                     continue;
                  }

                  $template = new ScheduleTemplate();
                  $template->setClass($class);
                  $template->setDay($day);
                  $template->setClassTime($classTime);
                  $template->setTeacher($this->random($this->teachers));
                  $template->setSubject($this->random($this->subjects));
                  $template->setPlace(rand(1, 399));

                  $this->em->persist($template);
               }
            }
         }

         $this->em->flush();
      }

      $io->success("Command ended successfully");

      return Command::SUCCESS;
   }

   private function checkRequirements(&$io)
   {
      $ok = true;

      if (empty($this->classTimes)) {
         $io->error("Class times doesn't exists. Run commnad: php bin/console db:init:classTime");
         $ok = false;
      }

      if (empty($this->classes)) {
         $io->error("Classes doesn't exists. Run commnad: php bin/console db:seed:class --limit 10");
         $ok = false;
      }

      if (empty($this->teachers)) {
         $io->error("Teachers doesn't exists. Run commnad: php bin/console db:seed:users --limit 100");
         $ok = false;
      }

      if (empty($this->subjects)) {
         $io->error("School subjects doesn't exists. Run commnad: php bin/console db:init:subject");
         $ok = false;
      }

      return $ok;
   }

   private function random($objects)
   {
      return $objects[array_rand($objects)];
   }
}