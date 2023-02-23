<?php

namespace App\DataFixtures\Factory;

use App\Entity\Lesson\LessonTime;
use Doctrine\ORM\EntityManagerInterface;

   class LessonTimeFactory
{
   private $repository;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->repository = $entityManager->getRepository(LessonTime::class);
   }

   public function create(string $from, string $to, bool $flush = false)
   {
      $lessonTime = new LessonTime();
      $lessonTime->setFromTime(new \DateTime($from));
      $lessonTime->setToTime(new \DateTime($to));
      $this->repository->add($lessonTime, $flush);
   }
}