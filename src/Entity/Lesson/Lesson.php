<?php

namespace App\Entity\Lesson;

use App\Repository\Lesson\LessonRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonRepository::class)
 * @ORM\Table(name="lesson")
 */
class Lesson extends AbstractLesson
{
   /**
    * @ORM\ManyToOne(targetEntity=LessonStatus::class)
    * @ORM\JoinColumn(nullable=false, options={"default": 1})
    */
   private $lessonStatus;

   /**
    * @ORM\Column(type="date")
    */
   private $date;

   public function getLessonStatus(): ?LessonStatus
   {
      return $this->lessonStatus;
   }

   public function setLessonStatus(?LessonStatus $lessonStatus): self
   {
      $this->lessonStatus = $lessonStatus;

      return $this;
   }

   public function getDate(): ?DateTime
   {
      return $this->date;
   }

   public function setDate(DateTime $date): self
   {
      $this->date = $date;

      return $this;
   }
}