<?php

namespace App\Repository\SchoolClass;

use App\Entity\SchoolClass\SchoolClassStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolClassRepository>
 *
 * @method SchoolClassStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolClassStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolClassStatus[]    findAll()
 * @method SchoolClassStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolClassStatusRepository extends ServiceEntityRepository
{
   public function __construct(ManagerRegistry $registry)
   {
      parent::__construct($registry, SchoolClassStatus::class);
   }

   public function add(SchoolClassStatus $entity, bool $flush = false): void
   {
      $this->getEntityManager()->persist($entity);

      if ($flush) {
         $this->getEntityManager()->flush();
      }
   }

   public function remove(SchoolClassStatus $entity, bool $flush = false): void
   {
      $this->getEntityManager()->remove($entity);

      if ($flush) {
         $this->getEntityManager()->flush();
      }
   }
}