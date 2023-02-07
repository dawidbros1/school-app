<?php

namespace App\Entity\UserType;

use App\Entity\Role;
use App\Entity\SchoolClass\SchoolClass;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserType\TeacherRepository;
use App\Interfaces\CustomUserInterface as UserInterface;

/**
 * @ORM\Entity(repositoryClass=TeacherRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Teacher extends User implements UserInterface, PasswordAuthenticatedUserInterface
{
   private static $role = null;

   /**
    * @ORM\OneToOne(targetEntity="App\Entity\SchoolClass\SchoolClass", mappedBy="teacher")
    * @ORM\JoinColumn(nullable=true)
    */
   private $class;

   /**
    * @see UserInterface
    */
   public function getRoles(): array
   {
      $roles = parent::getRoles();
      $roles[] = 'ROLE_TEACHER';
      return array_unique($roles);
   }

   public static function getRole()
   {
      if (self::$role == null) {
         self::$role = new Role("teacher", "Nauczyciel");
      }

      return self::$role;
   }

   public function getClass(): ?SchoolClass
   {
      return $this->class;
   }

   public function setClass(?SchoolClass $class): void
   {
      $this->class = $class;
   }
}