<?php

namespace App\Repository\Schedule;

use App\Entity\Schedule\ClassTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClassTime>
 *
 * @method ClassTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClassTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClassTime[]    findAll()
 * @method ClassTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassTime::class);
    }

    public function add(ClassTime $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ClassTime $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}