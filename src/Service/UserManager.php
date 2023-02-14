<?php

namespace App\Service;

use App\Entity\UserType\Admin;
use App\Entity\UserType\Owner;
use App\Entity\UserType\Student;
use App\Entity\UserType\Teacher;
use App\Enum\UserType;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
   private $em;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
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
}