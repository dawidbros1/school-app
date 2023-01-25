<?php

namespace App\DataFixtures\Factory;

use App\Entity\Roles;
use App\Entity\User;
use App\Service\EmailGenerator;
use App\Service\UserCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
   private $userRepository;
   private $faker;
   private $roles;
   private $emailGenerator, $codeGenerator;
   private $userPasswordHasher;
   public function __construct(EntityManagerInterface $entityManager, EmailGenerator $emailGenerator, UserPasswordHasherInterface $userPasswordHasher, UserCodeGenerator $codeGenerator)
   {
      $this->userRepository = $entityManager->getRepository(User::class);
      $this->roles = $entityManager->getRepository(Roles::class)->findAll();
      $this->faker = Factory::create();
      $this->userPasswordHasher = $userPasswordHasher;

      $this->emailGenerator = $emailGenerator;
      $this->codeGenerator = $codeGenerator;

      $this->checkTheRequirements();

      array_shift($this->roles); // REMOVE ROLE: ROLE_OWNER
   }

   private function checkTheRequirements()
   {
      if (count($this->roles) == 0) {
         //! ROLE MUST EXISTS
      }
   }

   private function randRole()
   {
      return [$this->roles[array_rand($this->roles)]->getName()];
   }

   public function create(array $data = [], bool $flush = false)
   {
      $user = new User();
      $user->setFirstName($data['firstName'] ?? $this->faker->firstName);
      $user->setLastName($data['lastName'] ?? $this->faker->lastName);
      $user->setPesel($data['pesel'] ?? $this->faker->unique()->numberBetween(10000000000, 99999999999));
      $user->setRoles($data['roles'] ?? $this->randRole());
      $user->setEmail($data['email'] ?? $this->emailGenerator->generate($user));

      if ($data['password'] ?? false) {
         $hash = $this->userPasswordHasher->hashPassword($user, $data['password']);
         $user->setPassword($hash);
      } else {
         $user->setCode($this->codeGenerator->generate($user));
      }

      $this->userRepository->add($user, $flush);
   }
}