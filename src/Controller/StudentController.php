<?php

namespace App\Controller;

use App\Entity\Lesson\LessonStatus;
use App\Entity\Lesson\LessonTime;
use App\Service\Shared\ScheduleSharedCode;
use App\Service\User\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

/**
 * @IsGranted("ROLE_STUDENT")
 * @Route("/student")
 */
class StudentController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/schedule", name = "app_student_schedule")
    */
   public function schedule(Request $request, UserManager $userManager, ScheduleSharedCode $code)
   {
      $class = $userManager->getUser()->getClass();
      $date = new \DateTime($request->get('date', 'now'));
      [$schedules, $prevDate, $nextDate] = $code->getData($class, $date);

      return $this->render('schedule/show.html.twig', [
         'class' => $class,
         'schedules' => $schedules,
         'lessonTimes' => $this->em->getRepository(LessonTime::class)->findAll(),
         'lessonStatuses' => $this->em->getRepository(LessonStatus::class)->findAll(),
         'nextPage' => $this->generateUrl("app_student_schedule", ['date' => $nextDate->format("Y-m-d")]),
         'prevPage' => $this->generateUrl("app_student_schedule", ['date' => $prevDate->format("Y-m-d")]),
         'back' => $this->generateUrl("app_dashboard"),
         'backButtonText' => "Powrót do Dashboard"
      ]);
   }

   /**
    * @Route("/schedule/mobile", name = "app_student_schedule_mobile")
    */
   public function mobileSchedule(Request $request, UserManager $userManager, ScheduleSharedCode $code)
   {
      $class = $userManager->getUser()->getClass();
      $date = new \DateTime($request->get('date', 'now'));
      $lessonTimes = $this->em->getRepository(LessonTime::class)->findAll();
      [$schedules, $prevDate, $nextDate] = $code->getData($class, $date);

      foreach ($schedules as $schedule) {
         $schedule->sortBy($lessonTimes);
      }

      return $this->render('student/schedule/mobile.html.twig', [
         'class' => $class,
         'schedules' => $schedules,
         'lessonTimes' => $lessonTimes,
         'lessonStatuses' => $this->em->getRepository(LessonStatus::class)->findAll(),
         'nextPage' => $this->generateUrl("app_student_schedule_mobile", ['date' => $nextDate->format("Y-m-d")]),
         'prevPage' => $this->generateUrl("app_student_schedule_mobile", ['date' => $prevDate->format("Y-m-d")]),
         'back' => $this->generateUrl("app_dashboard"),
         'backButtonText' => "Powrót do Dashboard"
      ]);
   }
}