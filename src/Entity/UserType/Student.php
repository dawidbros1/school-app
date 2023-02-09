<?php

namespace App\Entity\UserType;

use App\Entity\Role;
use App\Entity\SchoolClass\SchoolClass;
use App\Entity\User;
use App\Enum\UserRoles;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\UserType\StudentRepository;
use App\Interfaces\CustomUserInterface as UserInterface;

/**
 * @ORM\Entity(repositoryClass=StudentRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Student extends User implements UserInterface, PasswordAuthenticatedUserInterface
{
   private static $role = null;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\SchoolClass\SchoolClass", inversedBy="students")
    * @ORM\JoinColumn(nullable=true, name="class_id", referencedColumnName="id", onDelete="SET NULL")
    */
   private $class;

   /**
    * @see UserInterface
    */
   public function getRoles(): array
   {
      $roles = parent::getRoles();
      $roles[] = 'ROLE_STUDENT';
      return array_unique($roles);
   }

   public static function getRole()
   {
      if (self::$role == null) {
         self::$role = new Role(UserRoles::STUDENT, "Uczeń");
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