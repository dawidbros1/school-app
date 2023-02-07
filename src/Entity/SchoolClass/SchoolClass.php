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
    * @ORM\ManyToOne(targetEntity="App\Entity\UserType\Teacher")
    * @ORM\JoinColumn(nullable=true)
    */
   private $teacher;

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

   public function setName(string $name): void
   {
      $this->name = $name;
   }

   public function getTeacher(): ?Teacher
   {
      return $this->teacher;
   }

   public function setTeacher(?Teacher $teacher)
   {
      $this->teacher = $teacher;
   }

   public function getStatus(): SchoolClassStatus
   {
      return $this->status;
   }

   public function setStatus(SchoolClassStatus $status): void
   {
      $this->status = $status;
   }
}