<?php

namespace App\Entity\Schedule;

use App\Entity\Lesson\Lesson;

class Schedule
{
   private $lessons = [];

   public function __construct(array $lessons = [])
   {
      $this->lessons = $lessons;
   }

   public function getLessons(): array
   {
      return $this->lessons;
   }

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