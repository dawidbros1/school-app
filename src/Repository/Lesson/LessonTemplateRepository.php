<?php

namespace App\Repository\Lesson;

use App\Entity\Lesson\LessonTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LessonTemplate>
 *
 * @method LessonTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonTemplate[]    findAll()
 * @method LessonTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonTemplate::class);
    }

    public function add(LessonTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LessonTemplate $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}