<?php

namespace App\Controller\Schedule;

use App\Entity\Schedule\Schedule;
use App\Entity\Schedule\ScheduleTemplate;
use App\Entity\SchoolClass\SchoolClass;
use App\Form\Schedule\InitializerFormType;
use App\Form\Schedule\ScheduleTemplateFormType;
use App\Service\FormBuilder;
use App\Service\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/schedule")
 */
class ScheduleController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   private function getClass($id)
   {
      $class = $this->em->getRepository(SchoolClass::class)->findOneBy(['id' => $id]);

      if (!$class) {
         dump("klasa nie istnieje");
         die();
      }

      return $class;
   }
}