<?php

namespace App\Service\Entity;

use App\Entity\SchoolClass\SchoolClass;
use Doctrine\ORM\EntityManagerInterface;

class EntityProvider
{
   private $em;

   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   public function getSchoolClass($id)
   {
      $class = $this->em->getRepository(SchoolClass::class)->findOneBy(['id' => $id]);

      if (!$class) {
         dump("klasa nie istnieje");
         die();
      }

      return $class;
   }
}