<?php

namespace App\Entity\Schedule;

abstract class AbstractSchedule
{
   protected $lessons = [];

   public function __construct(array $lessons = [])
   {
      $this->lessons = $lessons;
   }

   public function getLessons(): array
   {
      return $this->lessons;
   }

   public function isEmpty()
   {
      return (bool) (count($this->lessons) == 0);
   }

   public function include(array $lessonTimes = [])
   {
      $lessons = $this->lessons;
      $selectedLesson = null;
      $output = [];

      foreach ($lessonTimes as $lessonTime) {
         $selectedLesson = null;

         foreach ($lessons as $lesson) {
            if ($lessonTime == $lesson->getLessonTime()) {
               $selectedLesson = $lesson;
            }
         }

         $output[] = $selectedLesson;
      }

      $this->lessons = $output;
   }
}