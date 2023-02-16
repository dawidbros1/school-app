<?php

namespace App\Repository;

use App\Entity\SchoolSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolSubject>
 *
 * @method SchoolSubject|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolSubject|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolSubject[]    findAll()
 * @method SchoolSubject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolSubjectRepository extends ServiceEntityRepository
{
   public function __construct(ManagerRegistry $registry)
   {
      parent::__construct($registry, SchoolSubject::class);
   }

   public function add(SchoolSubject $entity, bool $flush = false): void
   {
      $this->getEntityManager()->persist($entity);

      if ($flush) {
         $this->getEntityManager()->flush();
      }
   }

   public function remove(SchoolSubject $entity, bool $flush = false): void
   {
      $this->getEntityManager()->remove($entity);

      if ($flush) {
         $this->getEntityManager()->flush();
      }
   }
}