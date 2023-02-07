<?php

namespace App\Entity\SchoolClass;

use App\Repository\SchoolClass\SchoolClassStatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SchoolClassStatusRepository::class)
 */
class SchoolClassStatus
{
   const ACTIVE = "active";
   const ARCHIVED = "archived";

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
    * @ORM\OneToMany(targetEntity="App\Entity\SchoolClass\SchoolClass", mappedBy="status") 
    */
   private $class;

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

   public function getClass(): SchoolClass
   {
      return $this->class;
   }

   public function setClass(SchoolClass $class): self
   {
      $this->class = $class;

      return $this;
   }
}