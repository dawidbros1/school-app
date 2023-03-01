<?php

namespace App\Entity\Schedule;

use App\Entity\Lesson\Lesson;

class Schedule extends AbstractSchedule
{
   private $date;

   public function __construct(array $lessons = [], ?\DateTime $date = null)
   {
      $this->date = $date;
      parent::__construct($lessons);
   }

   public function addLesson(Lesson $lesson)
   {
      $this->lessons[] = $lesson;
   }

   public function canManage()
   {
      if ($this->date != null) {
         $date = $this->date->format("Y-m-d");
      } else if (!empty($this->lessons)) {
         $date = $this->lessons[0]->getDate()->format("Y-m-d");
      } else {
         return false;
      }

      $today = (new \DateTime('now'))->format("Y-m-d");
      return (bool) ($date >= $today);
   }

   public function getDate()
   {
      return $this->date;
   }
}