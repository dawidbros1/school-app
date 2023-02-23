<?php

namespace App\Service\User;

use App\Entity\UserType\Admin;
use App\Entity\UserType\Owner;
use App\Entity\UserType\Student;
use App\Entity\UserType\Teacher;
use App\Enum\UserType;
use App\Interfaces\CustomUserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UserManager
{
   private $em;
   private $security;

   public function __construct(EntityManagerInterface $entityManager, Security $security)
   {
      $this->em = $entityManager;
      $this->security = $security;
   }

   public function getEntity(string $userType)
   {
      switch ($userType) {
         case UserType::STUDENT:
            return new Student();
         case UserType::TEACHER:
            return new Teacher();
         case UserType::ADMIN:
            return new Admin();
         case UserType::OWNER:
            return new Owner();
      }
   }

   public function getRepository(string $userType)
   {
      switch ($userType) {
         case 'student':
            return $this->em->getRepository(Student::class);
         case 'teacher':
            return $this->em->getRepository(Teacher::class);
         case 'admin':
            return $this->em->getRepository(Admin::class);
         case 'owner':
            return $this->em->getRepository(Owner::class);
      }
   }

   public function getUser(): ?CustomUserInterface
   {
      return $this->security->getUser();
   }
}