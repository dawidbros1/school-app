<?php

namespace App\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface;

interface CustomUserInterface extends UserInterface
{
   public function getEmail();

   public function getFirstName();

   public function getLastName();

   public function getPesel();
}