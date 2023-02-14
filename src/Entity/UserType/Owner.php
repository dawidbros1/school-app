<?php

namespace App\Entity\UserType;

use App\Entity\Role;
use App\Entity\User;
use App\Enum\UserType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserType\OwnerRepository;
use App\Interfaces\CustomUserInterface as UserInterface;

/**
 * @ORM\Entity(repositoryClass=OwnerRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Owner extends User implements UserInterface, PasswordAuthenticatedUserInterface
{
   private static $role = null;

   /**
    * @see UserInterface
    */
   public function getRoles(): array
   {
      $roles = parent::getRoles();
      $roles[] = 'ROLE_OWNER';
      return array_unique($roles);
   }

   public static function getRole()
   {
      if (self::$role == null) {
         self::$role = new Role(UserType::OWNER, "Właściciel");
      }

      return self::$role;
   }
}