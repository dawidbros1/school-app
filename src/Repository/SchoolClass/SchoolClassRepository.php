<?php

namespace App\Repository\SchoolClass;

use App\Entity\SchoolClass\SchoolClass;
use App\Entity\SchoolClass\SchoolClassStatus;
use App\Entity\UserType\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SchoolClassRepository>
 *
 * @method SchoolClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolClass[]    findAll()
 * @method SchoolClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolClassRepository extends ServiceEntityRepository
{
   public function __construct(ManagerRegistry $registry)
   {
      parent::__construct($registry, SchoolClass::class);
   }

   public function add(SchoolClass $entity, bool $flush = false): void
   {
      $this->getEntityManager()->persist($entity);

      if ($flush) {
         $this->getEntityManager()->flush();
      }
   }

   public function remove(SchoolClass $entity, bool $flush = false): void
   {
      $this->getEntityManager()->remove($entity);

      if ($flush) {
         $this->getEntityManager()->flush();
      }
   }

   public function getActiveClasses()
   {
      return $this->createQueryBuilder('c')
         ->where("c.status = :status_id")
         ->setParameter("status_id", SchoolClassStatus::ACTIVE);
   }

   public function getActiveClasssesWithoutTutor(Teacher $teacher)
   {
      return $this->createQueryBuilder('c')
         ->where("c.status = :status_id AND c.teacher IS NULL OR c.teacher = :teacher")
         ->setParameters([
            'status_id' => SchoolClassStatus::ACTIVE,
            'teacher' => $teacher
         ]);
   }
}