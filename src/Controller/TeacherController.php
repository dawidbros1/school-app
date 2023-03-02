<?php

namespace App\Controller;

use App\Entity\Lesson\Lesson;
use App\Entity\Lesson\LessonStatus;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\Schedule;
use App\Service\Shared\ScheduleSharedCode;
use App\Service\User\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

/**
 * @IsGranted("ROLE_TEACHER")
 * @Route("/teacher")
 */
class TeacherController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/schedule", name = "app_teacher_schedule")
    */
   public function schedule(Request $request, UserManager $userManager, ScheduleSharedCode $code)
   {
      $user = $userManager->getUser();
      $date = new \DateTime($request->get('date', 'now'));
      $dates = $code->getWeek($date->format("Y-m-d"));

      $lessons = $this->em->getRepository(Lesson::class)->teacher($user, $dates);
      $lessonTimes = $this->em->getRepository(LessonTime::class)->findAll();

      $nextDate = clone $date->modify("+7 days");
      $prevDate = clone $date->modify("-14 days");

      $schedules = [];

      foreach ($dates as $date) {
         $schedules[] = new Schedule([], $date);
      }

      foreach ($lessons as $lesson) {
         $N = $lesson->getDate()->format('N') - 1;
         $schedules[$N]->addLesson($lesson);
      }

      foreach ($schedules as $schedule) {
         $schedule->include($lessonTimes);
      }

      return $this->render("teacher/schedule.html.twig", [
         'title' => "Plan zajęć",
         'schedules' => $schedules,
         'lessonTimes' => $lessonTimes,
         'lessonStatuses' => $this->em->getRepository(LessonStatus::class)->findAll(),
         'nextPage' => $this->generateUrl("app_teacher_schedule", ['date' => $nextDate->format("Y-m-d")]),
         'prevPage' => $this->generateUrl("app_teacher_schedule", ['date' => $prevDate->format("Y-m-d")]),
         'back' => $this->generateUrl("app_dashboard"),
         'backButtonText' => "Powrót do Dashboard"
      ]);
   }
}