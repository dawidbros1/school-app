<?php

namespace App\Entity\UserType;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserType\AdminRepository;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Admin extends User implements UserInterface, PasswordAuthenticatedUserInterface
{
   /**
    * @see UserInterface
    */
   public function getRoles(): array
   {
      $roles = parent::getRoles();
      $roles[] = 'ROLE_ADMIN';
      return array_unique($roles);
   }
}