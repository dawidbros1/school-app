<?php

namespace App\Entity\Schedule;

use App\Entity\Lesson\Lesson;

class Schedule
{
   private $lessons = [];

   public function getLessons(): array
   {
      return $this->lessons;
   }

   public function addLesson(Lesson $lesson)
   {
      $this->lessons[] = $lesson;
   }
}