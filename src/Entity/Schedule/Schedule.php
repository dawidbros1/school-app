<?php

namespace App\Entity\Schedule;

use App\Entity\Lesson\Lesson;

class Schedule extends AbstractSchedule
{
   public function addLesson(Lesson $lesson)
   {
      $this->lessons[] = $lesson;
   }

   public function canManage()
   {
      // assumption: all lessons has this same date
      if (!empty($this->lessons)) {
         $date = $this->lessons[0]->getDate()->format("Y-m-d");
         $today = (new \DateTime('now'))->format("Y-m-d");
         return (bool) ($date >= $today);
      }

      return false;
   }
}