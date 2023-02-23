<?php

namespace App\Service\User;

use App\Interfaces\CustomUserInterface as UserInterface;

class EmailGenerator
{
   public function generate(UserInterface $user)
   {
      $firstName = $user->getFirstName();
      $lastName = $user->getLastName();
      $domain = "@domain.com";

      if (in_array('ROLE_STUDENT', $user->getRoles())) {

         if ($user->getEmail() != null) {
            $index = preg_replace("/[^0-9]/", "", $user->getEmail());
         } else {
            $index = rand(10000, 99999); // IN FUTURE USER COUNT + 10000
         }

         $email = strtolower(substr($lastName, 0, 1) . substr($firstName, 0, 1) . $index);
      } else {
         $email = strtolower(substr($firstName, 0, 1) . $lastName);
      }

      return ($email . $domain);
   }
}