<?php

namespace App\Entity\SchoolClass;

use App\Repository\SchoolClass\SchoolClassRepository;
use App\Entity\UserType\Teacher;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SchoolClassRepository::class)
 */
class SchoolClass
{
   /**
    * @ORM\Id
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
   private $id;

   /**
    * @ORM\Column(type="string", length=255)
    */
   private $name;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\UserType\Teacher", inversedBy="schoolClass")
    * @ORM\JoinColumn(nullable=true, name="teacher_id", referencedColumnName="id", onDelete="SET NULL")
    */
   private $teacher;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\UserType\Student", mappedBy="class")
    * @ORM\JoinColumn(onDelete="SET NULL")
    */
   private $students;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\SchoolClass\SchoolClassStatus")
    * @ORM\JoinColumn(nullable=false)
    */
   private $status;

   public function getId(): int
   {
      return $this->id;
   }

   public function getName(): string
   {
      return $this->name;
   }

   public function setName(string $name): self
   {
      $this->name = $name;

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

   public function updateTeacherClass()
   {
      if ($this->teacher != null) {
         $this->teacher->setClass($this);
      }
   }

   public function getStudents()
   {
      return $this->students;
   }

   public function setStudents(array $students): self
   {
      $this->students = $students;

      return $this;
   }

   public function getStatus(): SchoolClassStatus
   {
      return $this->status;
   }

   public function setStatus(SchoolClassStatus $status): self
   {
      $this->status = $status;

      return $this;
   }
}