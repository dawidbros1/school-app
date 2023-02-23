<?php

namespace App\Repository\Lesson;

use App\Entity\Lesson\LessonTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LessonTime>
 *
 * @method LessonTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonTime[]    findAll()
 * @method LessonTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonTime::class);
    }

    public function add(LessonTime $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LessonTime $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}