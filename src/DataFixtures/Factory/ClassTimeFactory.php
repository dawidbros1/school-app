<?php

namespace App\DataFixtures\Factory;

use App\Entity\Schedule\ClassTime;
use Doctrine\ORM\EntityManagerInterface;

class ClassTimeFactory
{
   private $repository;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->repository = $entityManager->getRepository(ClassTime::class);
   }

   public function create(string $from, string $to, bool $flush = false)
   {
      $classTime = new ClassTime();
      $classTime->setFromTime(new \DateTime($from));
      $classTime->setToTime(new \DateTime($to));
      $this->repository->add($classTime, $flush);
   }
}