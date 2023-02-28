<?php

namespace App\Entity\Lesson;

use App\Repository\Lesson\LessonStatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonStatusRepository::class)
 * @ORM\Table(name="lesson_status")
 */
class LessonStatus
{
   /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
   private $id;

   /**
    * @ORM\Column(type="string", length=64)
    */
   private $name;

   /**
    * @ORM\Column(type="string", length=64)
    */
   private $description;

   public function __construct(int $id, string $name, string $description)
   {
      $this->id = $id;
      $this->name = $name;
      $this->description = $description;
   }

   public function getId(): int
   {
      return $this->id;
   }

   public function getDescription(): string
   {
      return $this->description;
   }

   public function getName(): string
   {
      return $this->name;
   }
}