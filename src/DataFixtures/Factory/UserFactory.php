<?php

namespace App\DataFixtures\Factory;

use App\Enum\UserRoles;
use App\Service\EmailGenerator;
use App\Service\UserCodeGenerator;
use App\Service\UserManager;
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

      if ($number >= 1 && $number < ($max * 0.01)) {
         return UserRoles::ADMIN;
      } else if ($number >= ($max * 0.01) && $number < ($max * 0.03)) {
         return UserRoles::TEACHER;
      } else
         return UserRoles::STUDENT;
   }
}