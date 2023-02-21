<?php

namespace App\Entity\Schedule;

use App\Entity\SchoolClass\SchoolClass;
use App\Entity\SchoolSubject;
use App\Entity\UserType\Teacher;
use App\Repository\Schedule\ScheduleRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\Entity(repositoryClass=ScheduleRepository::class)
 */
class Schedule
{
   /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
   private $id;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\SchoolClass\SchoolClass")
    */
   private $class;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Schedule\ClassTime")
    */
   private $classTime;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\UserType\Teacher")
    * @ORM\JoinColumn(nullable=true)
    */
   private $teacher;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\SchoolSubject")
    * @ORM\JoinColumn(nullable=true)
    */
   private $subject;

   /**
    * @ORM\Column(type="string", length=64)
    */
   private $place;

   /**
    * @ORM\Column(type="date")
    */
   private $date;

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

   public function getClassTime(): ?ClassTime
   {
      return $this->classTime;
   }

   public function setClassTime(ClassTime $classTime)
   {
      $this->classTime = $classTime;

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