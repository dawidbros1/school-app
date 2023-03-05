<?php

namespace App\Service\Shared;

use App\Entity\Lesson\Lesson;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\Schedule;
use App\Entity\SchoolClass\SchoolClass;
use App\Entity\UserType\Teacher;
use Doctrine\ORM\EntityManagerInterface;

class ScheduleSharedCode
{
   private $em;
   public function __construct(EntityManagerInterface $em)
   {
      $this->em = $em;
   }

   public function getData(string $type, object $object, \DateTime $date)
   {
      $dates = $this->getWeek($date->format("Y-m-d"));

      if ($type == "teacher") {
         $lessons = $this->em->getRepository(Lesson::class)->teacher($object, $dates);
      } else if ($type == "class") {
         $lessons = $this->em->getRepository(Lesson::class)->class($object, $dates);
      } else {
         dump("Invalid type");
         die();
      }

      $lessonTimes = $this->em->getRepository(LessonTime::class)->findAll();

      $nextDate = clone $date->modify("+7 days");
      $prevDate = clone $date->modify("-14 days");

      $schedules = [];

      foreach ($dates as $date) {
         $schedules[] = new Schedule([], $date);
      }

      foreach ($lessons as $lesson) {
         $N = $lesson->getDate()->format('N') - 1;
         $schedules[$N]->addLesson($lesson);
      }

      foreach ($schedules as $schedule) {
         $schedule->include($lessonTimes);
      }

      return [$schedules, $prevDate, $nextDate, $lessonTimes];
   }

   private function getWeek($date)
   {
      $date = new \DateTime($date);
      $dates = [];

      while ($date->format("N") != 1) {
         $date->modify("-1 day");
      }

      while ($date->format("N") <= 5) {
         $dates[] = clone ($date);
         $date->modify("+1 day");
      }

      return $dates;
   }
}