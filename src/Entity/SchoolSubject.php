<?php

namespace App\Entity;

use App\Interfaces\CustomUserInterface as UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SchoolSubjectRepository;

/**
 * @ORM\Entity(repositoryClass=SchoolSubjectRepository::class)
 */
class SchoolSubject
{
   /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
   private $id;

   /**
    * @ORM\Column(type="string", length=180)
    * @Assert\Length(min=2, minMessage="Nazwa przedmiotu musi mieć co najmniej {{ limit }} znaków")
    * @Assert\Length(max=64, maxMessage="Nazwa przedmiotu nie może mieć więcej niż {{ limit }} znaków")
    */
   private $name;

   /**
    * @ORM\Column(type="integer")
    */
   private $status_id;

   public function getId(): ?int
   {
      return $this->id;
   }

   public function getName()
   {
      return $this->name;
   }

   public function setName($name): self
   {
      $this->name = $name;

      return $this;
   }

   public function getStatus()
   {
      return $this->status_id;
   }

   public function setStatus($status_id): self
   {
      $this->status_id = $status_id;

      return $this;
   }
}