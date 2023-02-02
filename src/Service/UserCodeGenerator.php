<?php

namespace App\Service;

use App\Interfaces\CustomUserInterface as UserInterface;

class UserCodeGenerator
{
   public function generate(UserInterface $user)
   {
      return hash('md5', time() + $user->getPesel());
   }
}