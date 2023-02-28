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
   private $description;

   /**
    * @ORM\Column(type="string", length=64)
    */
   private $backgorund;

   public function __construct(int $id, string $description, string $backgorund)
   {
      $this->id = $id;
      $this->description = $description;
      $this->backgorund = $backgorund;
   }

   public function getId(): int
   {
      return $this->id;
   }

   public function getDescription(): string
   {
      return $this->description;
   }

   public function getBackgorund(): string
   {
      return $this->backgorund;
   }
}