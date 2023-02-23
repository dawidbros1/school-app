<?php

namespace App\Entity\Lesson;

use App\Repository\Lesson\LessonTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonTemplateRepository::class)
 * @ORM\Table(name="lesson_template")
 */
class LessonTemplate extends AbstractLesson
{
   /**
    * @ORM\Column(type="string", length=64)
    */
   private $day;

   public function getDay(): string
   {
      return $this->day;
   }

   public function setDay(string $day): self
   {
      $this->day = $day;

      return $this;
   }
}