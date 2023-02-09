<?php

namespace App\DataFixtures\Factory;

use App\Entity\SchoolClass\SchoolClass;
use App\Entity\SchoolClass\SchoolClassStatus;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;

class SchoolClassFactory
{
   private $em;
   private $faker;
   private $status;
   private $repository;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
      $this->faker = Factory::create();
      $this->status = $this->em->getRepository(SchoolClassStatus::class)->findOneBy(['id' => SchoolClassStatus::ACTIVE]);
      $this->repository = $this->em->getRepository(SchoolClass::class);
   }

   public function create(array $data = [], bool $flush = false)
   {
      $class = new SchoolClass();
      $class->setName($data['firstName'] ?? $this->faker->words(3, true));
      $class->setStatus($this->status);

      $this->repository->add($class, $flush);
   }
}