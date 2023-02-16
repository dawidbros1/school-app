<?php

namespace App\Interfaces;

use App\Entity\SchoolClass\SchoolClass;
use Symfony\Component\Security\Core\User\UserInterface;

interface CustomUserInterface extends UserInterface
{
   public function getEmail();
   public function setEmail(string $email);

   public function getFirstName();
   public function setFirstName(string $firstName);

   public function getLastName();
   public function setLastName(string $lastName);

   public function getPesel();
   public function setPesel(string $pesel);

   public function setPassword(?string $password);

   public static function getRole();
   public function getClass(): ?SchoolClass;
}