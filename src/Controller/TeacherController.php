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
    * @Route("/schedule/{device}", name = "app_teacher_schedule")
    */
   public function schedule(Request $request, UserManager $userManager, ScheduleSharedCode $code)
   {
      $user = $userManager->getUser();
      $date = new \DateTime($request->get('date', 'now'));
      [$schedules, $prevDate, $nextDate, $lessonTimes] = $code->getData("teacher", $user, $date);

      $device = $request->get("device");

      return $this->render("schedule/user/" . $device . ".html.twig", [
         'title' => "Plan zajęć",
         'schedules' => $schedules,
         'display' => "class",
         'lessonTimes' => $lessonTimes,
         'lessonStatuses' => $this->em->getRepository(LessonStatus::class)->findAll(),
         'nextPage' => $this->generateUrl("app_teacher_schedule", ['date' => $nextDate->format("Y-m-d"), 'device' => $device]),
         'prevPage' => $this->generateUrl("app_teacher_schedule", ['date' => $prevDate->format("Y-m-d"), 'device' => $device]),
         'back' => $this->generateUrl("app_dashboard"),
         'backButtonText' => "Powrót do Dashboard"
      ]);
   }
}