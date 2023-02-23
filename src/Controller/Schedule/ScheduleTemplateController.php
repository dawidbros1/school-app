<?php

namespace App\Controller\Schedule;

use App\Entity\Lesson\LessonTemplate;
use App\Entity\Schedule\ScheduleTemplate;
use App\Service\Entity\EntityProvider;
use App\Service\Form\FormProvider;
use App\Service\Form\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/schedule/template")
 */
class ScheduleTemplateController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/show/{day}/{class_id}", name="app_scheduleTemplate_show")
    */
   public function show(Request $request, FormErrors $formErrors, FormProvider $formProvider, EntityProvider $entityProvider): Response
   {
      $day = $request->get('day');
      $class = $entityProvider->getSchoolClass($request->get('class_id'));
      $lesson = new LessonTemplate();

      $schedule = new ScheduleTemplate($this->em->getRepository(LessonTemplate::class)->findBy(['day' => $day, 'class' => $class]));
      $form = $formProvider->getLessonTemplateFormType($lesson, $class, $day, "Dodaj lekcje");

      return $this->render('schedule/template/show.html.twig', [
         'form' => $form->createView(),
         'initializerForm' => $formProvider->getInitializerFormType($day, $class)->createView(),
         'schedule' => $schedule,
         'day' => $day,
         'class_id' => $class->getId()
      ]);
   }
}