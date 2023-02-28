<?php

namespace App\Controller\Schedule;

use App\Entity\Lesson\LessonTemplate;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\ScheduleTemplate;
use App\Service\Entity\EntityProvider;
use App\Service\Form\FormErrors;
use App\Service\Form\Provider\LessonTemplateFormProvider;
use App\Service\Form\Provider\ScheduleDateRangeFormProvider;
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
   public function show(Request $request, FormErrors $formErrors, LessonTemplateFormProvider $lessonTemplateFormProvider, ScheduleDateRangeFormProvider $scheduleDateRangeFormProvider, EntityProvider $entityProvider): Response
   {
      $day = $request->get('day');
      $class = $entityProvider->getSchoolClass($request->get('class_id'));
      $lesson = new LessonTemplate();
      $lessonTimes = $this->em->getRepository(LessonTime::class)->findAll();

      $schedule = new ScheduleTemplate($this->em->getRepository(LessonTemplate::class)->findBy(['day' => $day, 'class' => $class]));
      $schedule->sortBy($lessonTimes);

      $form = $lessonTemplateFormProvider->getCreateFormType($lesson, $class, $day, [
         'lessonTimes' => $lessonTimes
      ]);

      $formErrors->load($form);

      return $this->render('schedule/template/show.html.twig', [
         'form' => $form->createView(),
         'scheduleDataRangeForm' => $scheduleDateRangeFormProvider->getFormType($day, $class)->createView(),
         'schedule' => $schedule,
         'day' => $day,
         'class_id' => $class->getId(),
         'lessonTimes' => $lessonTimes
      ]);
   }
}