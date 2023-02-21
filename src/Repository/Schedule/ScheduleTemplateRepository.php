<?php

namespace App\Repository\Schedule;

use App\Entity\Schedule\ScheduleTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScheduleTemplate>
 *
 * @method ScheduleTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScheduleTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScheduleTemplate[]    findAll()
 * @method ScheduleTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScheduleTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScheduleTemplate::class);
    }

    public function add(ScheduleTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ScheduleTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}