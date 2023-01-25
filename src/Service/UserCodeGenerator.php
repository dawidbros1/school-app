<?php

namespace App\Service;

use App\Entity\User;

class UserCodeGenerator
{
   public function generate(User $user)
   {
      return hash('md5', time() + $user->getPesel());
   }
}