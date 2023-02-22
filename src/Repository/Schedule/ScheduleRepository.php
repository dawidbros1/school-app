<?php

namespace App\Repository\Schedule;

use App\Entity\Schedule\Schedule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Schedule>
 *
 * @method Schedule|null find($id, $lockMode = null, $lockVersion = null)
 * @method Schedule|null findOneBy(array $criteria, array $orderBy = null)
 * @method Schedule[]    findAll()
 * @method Schedule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Schedule::class);
    }

    public function removeIn($class, $dates)
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

    public function getIn($class, $dates)
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

    public function add(Schedule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Schedule $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}