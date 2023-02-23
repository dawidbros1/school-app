<?php

namespace App\Entity\Lesson;

use App\Entity\Lesson\LessonTime;
use App\Entity\SchoolClass\SchoolClass;
use App\Entity\SchoolSubject;
use App\Entity\UserType\Teacher;
use Doctrine\ORM\Mapping as ORM;

abstract class AbstractLesson
{
   /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
   protected $id;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\SchoolClass\SchoolClass")
    */
   protected $class;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Lesson\LessonTime")
    */
   protected $lessonTime;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\UserType\Teacher")
    * @ORM\JoinColumn(nullable=true)
    */
   protected $teacher;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\SchoolSubject")
    * @ORM\JoinColumn(nullable=true)
    */
   protected $subject;

   /**
    * @ORM\Column(type="string", length=64)
    */
   protected $place;

   public function getId(): ?int
   {
      return $this->id;
   }

   public function getClass(): SchoolClass
   {
      return $this->class;
   }

   public function setClass(SchoolClass $class): self
   {
      $this->class = $class;

      return $this;
   }

   public function getLessonTime(): ?LessonTime
   {
      return $this->lessonTime;
   }

   public function setLessonTime(LessonTime $lessonTime)
   {
      $this->lessonTime = $lessonTime;

      return $this;
   }

   public function getTeacher(): ?Teacher
   {
      return $this->teacher;
   }

   public function setTeacher(?Teacher $teacher): self
   {
      $this->teacher = $teacher;

      return $this;
   }

   public function getSubject(): ?SchoolSubject
   {
      return $this->subject;
   }

   public function setSubject(?SchoolSubject $subject): self
   {
      $this->subject = $subject;

      return $this;
   }

   public function getPlace(): string
   {
      return $this->place;
   }

   public function setPlace(string $place): self
   {
      $this->place = $place;

      return $this;
   }
}