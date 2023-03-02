<?php

namespace App\Repository\Lesson;

use App\Entity\Lesson\Lesson;
use App\Entity\SchoolClass\SchoolClass;
use App\Entity\UserType\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lesson>
 *
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    public function removeIn(SchoolClass $class, array $dates)
    {
        $dates = array_map(function ($date) {
            return $date->format("Y-m-d");
        }, $dates);

        $this->createQueryBuilder('s')
            ->delete()
            ->where("s.date IN(:dates)")
            ->andWhere('s.class = :class')
            ->setParameter('class', $class)
            ->setParameter('dates', $dates)
            ->getQuery()
            ->execute();
    }

    public function getIn(SchoolClass $class, array $dates)
    {
        $dates = array_map(function ($date) {
            return $date->format("Y-m-d");
        }, $dates);

        return $this->createQueryBuilder('s')
            ->select()
            ->where("s.date IN(:dates)")
            ->andWhere('s.class = :class')
            ->setParameter('class', $class)
            ->setParameter('dates', $dates)
            ->getQuery()
            ->getResult();
    }

    public function teacher(Teacher $teacher, array $dates)
    {
        $dates = array_map(function ($date) {
            return $date->format("Y-m-d");
        }, $dates);

        return $this->createQueryBuilder('s')
            ->select()
            ->where("s.date IN(:dates)")
            ->andWhere('s.teacher = :teacher')
            ->setParameter('teacher', $teacher)
            ->setParameter('dates', $dates)
            ->getQuery()
            ->getResult();
    }

    public function add(Lesson $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lesson $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}