<?php

namespace App\DataFixtures\Factory;

use App\Entity\Roles;
use App\Entity\User;
use App\Service\EmailGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;

class RolesFactory
{
   private $repository;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->repository = $entityManager->getRepository(Roles::class);
   }

   public function create(array $data = [], bool $flush = false)
   {
      $roles = new Roles();
      $roles->setName($data['name']);
      $roles->setDescription($data['description']);

      $this->repository->add($roles, $flush);
   }
}