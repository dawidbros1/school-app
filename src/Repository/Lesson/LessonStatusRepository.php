<?php

namespace App\Repository\Lesson;

use App\Entity\Lesson\LessonStatus;
use App\Entity\SchoolClass\SchoolClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LessonStatus>
 *
 * @method LessonStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonStatus[]    findAll()
 * @method LessonStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonStatusRepository extends ServiceEntityRepository
{
   public function __construct(ManagerRegistry $registry)
   {
      parent::__construct($registry, LessonStatus::class);
   }

   public function add(LessonStatus $entity, bool $flush = false): void
   {
      $this->getEntityManager()->persist($entity);

      if ($flush) {
         $this->getEntityManager()->flush();
      }
   }

   public function remove(LessonStatus $entity, bool $flush = false): void
   {
      $this->getEntityManager()->remove($entity);

      if ($flush) {
         $this->getEntityManager()->flush();
      }
   }
}