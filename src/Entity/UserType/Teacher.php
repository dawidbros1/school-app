<?php

namespace App\Entity\UserType;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserType\TeacherRepository;

/**
 * @ORM\Entity(repositoryClass=TeacherRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Teacher extends User implements UserInterface, PasswordAuthenticatedUserInterface
{
   /**
    * @see UserInterface
    */
   public function getRoles(): array
   {
      $roles = parent::getRoles();
      $roles[] = 'ROLE_TEACHER';
      return array_unique($roles);
   }
}