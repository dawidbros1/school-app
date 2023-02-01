<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Role
{
   private $name;
   private $description;

   public function __construct(string $name, string $description)
   {
      $this->name = $name;
      $this->description = $description;
   }

   /**
    * Get the value of name
    */
   public function getName()
   {
      return $this->name;
   }

   /**
    * Set the value of name
    *
    * @return  self
    */
   public function setName($name)
   {
      $this->name = $name;

      return $this;
   }

   /**
    * Get the value of description
    */
   public function getDescription()
   {
      return $this->description;
   }

   /**
    * Set the value of description
    *
    * @return  self
    */
   public function setDescription($description)
   {
      $this->description = $description;

      return $this;
   }
}