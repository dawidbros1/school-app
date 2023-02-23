<?php

namespace App\Command\Fill;

use App\Entity\SchoolClass\SchoolClass;
use App\Entity\UserType\Student;
use App\Entity\UserType\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

# php bin/console db:fill:schoolClass

class SchoolClassFillCommand extends Command
{
   protected static $defaultName = 'db:fill:schoolClass';
   protected static $defaultDescription = 'The command assigning students to class and set class teacher.';
   private $em;
   private $classes;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
      $this->classes = $this->em->getRepository(SchoolClass::class)->findAll();

      parent::__construct();
   }

   protected function execute(InputInterface $input, OutputInterface $output): int
   {
      $io = new SymfonyStyle($input, $output);

      if (empty($this->classes)) {
         $io->info("Classes does't exists in system");
         $io->info("Please run command: [ php bin/console db:seed:class --limit 5 ] to create classes");
      } else {
         $this->assignStudentsToClass($io);
         $this->setClassTeacher($io);
         $this->em->flush();

         $io->success("Command ended successfully");
      }

      return Command::SUCCESS;
   }

   private function assignStudentsToClass(&$io)
   {
      $students = $this->em->getRepository(Student::class)->findBy(['class' => null]);

      if (empty($students)) {
         $io->info("All exists students are assigned to class");
      } else {
         foreach ($students as $student) {
            $student->setClass($this->getRandomClass());
            $this->em->persist($student);
         }
      }
   }

   private function setClassTeacher(&$io)
   {
      $teachers = $this->em->getRepository(Teacher::class)->findBy(['class' => null]); // students without class
      $classes = $this->em->getRepository(SchoolClass::class)->findBy(['teacher' => null]); // class with out class teacher

      if (empty($teachers)) {
         $io->info("All exists teachers are assigned to class.");
         (!empty($classes)) ? $io->info("Some classes do not have a class teacher.") : null;
      } else {
         if (empty($classes)) {
            $io->info("All exists classes have set teacher");
         } else {
            foreach ($classes as $class) {
               if (empty($teachers)) {
                  break;
               } else {
                  $teacher = array_pop($teachers);
                  $class->setTeacher($teacher);
                  $class->updateTeacherClass();
                  $this->em->persist($class);
               }
            }
         }
      }
   }

   private function getRandomClass()
   {
      return $this->classes[array_rand($this->classes)];
   }
}