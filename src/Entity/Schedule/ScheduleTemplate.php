<?php

namespace App\Entity\Schedule;

class ScheduleTemplate
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

   public function isEmpty()
   {
      return (bool) (count($this->lessons) == 0);
   }
}