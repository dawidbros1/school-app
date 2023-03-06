<?php

namespace App\DataFixtures\Factory;

use App\Enum\UserType;
use App\Service\User\EmailGenerator;
use App\Service\User\UserCodeGenerator;
use App\Service\User\UserManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
   private $faker;
   private $emailGenerator, $codeGenerator;
   private $userPasswordHasher;
   private $userManager;

   public function __construct(EmailGenerator $emailGenerator, UserPasswordHasherInterface $userPasswordHasher, UserCodeGenerator $codeGenerator, UserManager $userManager)
   {
      $this->faker = Factory::create();
      $this->userPasswordHasher = $userPasswordHasher;

      $this->emailGenerator = $emailGenerator;
      $this->codeGenerator = $codeGenerator;
      $this->userManager = $userManager;
   }

   public function create(array $data = [], bool $flush = false)
   {
      $user = $this->userManager->getEntity($data['roles'] ?? $this->randUserType());
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

      $repository = $this->userManager->getRepository($user->getRole()->getName());
      $repository->add($user, $flush);
   }

   private function randUserType()
   {
      $max = 1000;
      $number = rand(1, $max);

      if ($number >= 1 && $number < ($max * 0.02)) {
         return UserType::ADMIN;
      } else if ($number >= ($max * 0.02) && $number < ($max * 0.05)) {
         return UserType::TEACHER;
      } else
         return UserType::STUDENT;
   }
}