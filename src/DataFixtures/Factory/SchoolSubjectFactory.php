<?php

namespace App\DataFixtures\Factory;

use App\Entity\SchoolSubject;
use App\Enum\SchoolSubjectStatus;
use Doctrine\ORM\EntityManagerInterface;

class SchoolSubjectFactory
{
   private $repository;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->repository = $entityManager->getRepository(SchoolSubject::class);
   }

   public function create(string $name, bool $flush = false)
   {
      $subject = new SchoolSubject();
      $subject->setName($name ?? "Brak nazwy");
      $subject->setStatus(SchoolSubjectStatus::ACTIVE);
      $this->repository->add($subject, $flush);
   }
}