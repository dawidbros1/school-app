<?php

namespace App\DataFixtures\Factory;

use App\Entity\UserType\Admin;
use App\Entity\UserType\Owner;
use App\Entity\UserType\Student;
use App\Entity\UserType\Teacher;
use App\Service\EmailGenerator;
use App\Service\UserCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
   private $em;
   private $faker;
   private $emailGenerator, $codeGenerator;
   private $userPasswordHasher;

   private $userType = ['admin', 'teacher', 'student'];

   public function __construct(EntityManagerInterface $entityManager, EmailGenerator $emailGenerator, UserPasswordHasherInterface $userPasswordHasher, UserCodeGenerator $codeGenerator)
   {
      $this->em = $entityManager;
      $this->faker = Factory::create();
      $this->userPasswordHasher = $userPasswordHasher;

      $this->emailGenerator = $emailGenerator;
      $this->codeGenerator = $codeGenerator;
   }

   public function create(array $data = [], bool $flush = false)
   {
      $user = $this->getUser($data['roles'] ?? $this->randUserType());
      $user->setFirstName($data['firstName'] ?? $this->faker->firstName);
      $user->setLastName($data['lastName'] ?? $this->faker->lastName);
      $user->setPesel($data['pesel'] ?? $this->faker->unique()->numberBetween(10000000000, 99999999999));
      $user->setEmail($data['email'] ?? $this->emailGenerator->generate($user));

      if ($data['password'] ?? false) {
         $hash = $this->userPasswordHasher->hashPassword($user, $data['password']);
         $user->setPassword($hash);
      } else {
         $user->setCode($this->codeGenerator->generate($user));
      }

      $this->em->getRepository(get_class($user))->add($user, $flush);
   }

   private function getUser($userType)
   {
      switch ($userType) {
         case 'owner':
            return new Owner();
         case 'admin':
            return new Admin();
         case 'teacher':
            return new Teacher();
         case 'student':
            return new Student();
      }
   }

   private function randUserType()
   {
      return $this->userType[array_rand($this->userType)];
   }

}