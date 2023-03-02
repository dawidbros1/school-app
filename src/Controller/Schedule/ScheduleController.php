<?php

namespace App\Controller\Schedule;

use App\Entity\Lesson\Lesson;
use App\Entity\Lesson\LessonStatus;
use App\Entity\Lesson\LessonTemplate;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\Schedule;
use App\Service\Entity\EntityProvider;
use App\Service\Form\FormErrors;
use App\Service\Form\Provider\LessonFormProvider;
use App\Service\Form\Provider\ScheduleDateRangeFormProvider;
use App\Service\Shared\ScheduleSharedCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/class/schedule")
 */
class ScheduleController extends AbstractController
{
   private $em;
   private $entityProvider;
   public function __construct(EntityManagerInterface $entityManager, EntityProvider $entityProvider)
   {
      $this->em = $entityManager;
      $this->entityProvider = $entityProvider;
   }

   /**
    * @Route("/show/{class_id}", name="app_schedule_show")
    */
   public function show(Request $request, ScheduleSharedCode $code)
   {
      $class = $this->entityProvider->getSchoolClass($request->get('class_id'));
      $date = new \DateTime($request->get('date', 'now'));

      [$schedules, $prevDate, $nextDate] = $code->getData($class, $date);

      return $this->render('schedule/show.html.twig', [
         'title' => "Plan zajęć [ " . $class->getName() . " ]",
         'class' => $class,
         'schedules' => $schedules,
         'lessonTimes' => $this->em->getRepository(LessonTime::class)->findAll(),
         'lessonStatuses' => $this->em->getRepository(LessonStatus::class)->findAll(),
         'nextPage' => $this->generateUrl("app_schedule_show", ['class_id' => $class->getId(), 'date' => $nextDate->format("Y-m-d")]),
         'prevPage' => $this->generateUrl("app_schedule_show", ['class_id' => $class->getId(), 'date' => $prevDate->format("Y-m-d")]),
         'back' => $this->generateUrl("app_scheduleTemplate_show", ['class_id' => $class->getId(), 'day' => "monday"]),
         'backButtonText' => "Powrót do harmonogramu"
      ]);
   }

   /**
    * @Route("/manage/{class_id}/{date}", name="app_schedule_manage") 
    */
   public function manage(Request $request, LessonFormProvider $formProvider)
   {
      $date = new \DateTime($request->get('date'));
      $today = (new \DateTime('now'))->format("Y-m-d");

      if ($date->format("Y-m-d") < $today) {
         $this->addFlash("error", "Nie można zarządzać przeszłymi danymi");

         if ($HTTP_REFERER = $request->server->get("HTTP_REFERER")) {
            return $this->redirect($HTTP_REFERER);
         } else {
            return $this->redirectToRoute("app_class_schedule_show", ['class_id' => $request->get("class_id")]);
         }
      }

      $lesson = new Lesson();
      $class = $this->entityProvider->getSchoolClass($request->get('class_id'));
      $schedule = new Schedule($this->em->getRepository(Lesson::class)->findBy(['class' => $class, 'date' => $date]));

      $lessonTimes = $this->em->getRepository(LessonTime::class)->findAll();

      $form = $formProvider->getCreateFormType($lesson, $class, $date, [
         'lessonTimes' => $lessonTimes
      ]);


      $schedule->include($lessonTimes);

      return $this->render('schedule/manage.html.twig', [
         'form' => $form->createView(),
         'schedule' => $schedule,
         'class' => $class,
         'date' => $date->format("Y-m-d"),
         'lessonTimes' => $lessonTimes
      ]);
   }

   /**
    * @Route("/fill/{class_id}/{day}", name="app_schedule_fill")
    * @Method("POST")
    */
   public function fill(Request $request, FormErrors $formErrors, ScheduleDateRangeFormProvider $formProvider): Response
   {
      $day = $request->get('day');
      $class = $this->entityProvider->getSchoolClass($request->get('class_id'));

      $form = $formProvider->getFormType($day, $class);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $min = $form->get("from")->getData();
         $max = $form->get("to")->getData();

         $dates = [];
         $template = $this->em->getRepository(LessonTemplate::class)->findBy(['day' => $day, 'class' => $class]);

         while ($max >= $min) {
            if (ucfirst($day) == $min->format("l")) {
               $dates[] = clone ($min);
            }

            $min->modify("+1 day");
         }

         if (!empty($dates)) {
            $this->em->getRepository(Lesson::class)->removeIn($class, $dates);
         }

         $status = $this->em->getRepository(LessonStatus::class)->findOneBy(['id' => 1]);

         foreach ($dates as $date) {
            foreach ($template as $item) {
               $lesson = new Lesson();
               $lesson->setClass($item->getClass());
               $lesson->setLessonTime($item->getLessonTime());
               $lesson->setTeacher($item->getTeacher());
               $lesson->setSubject($item->getSubject());
               $lesson->setPlace($item->getPlace());
               $lesson->setDate($date);
               $lesson->setLessonStatus($status);

               $this->em->persist($lesson);
            }
         }

         $this->em->flush();
         $this->addFlash('success', "Harmonogram został wygenerowany");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_scheduleTemplate_show', ['day' => $day, 'class_id' => $class->getId()]);
   }
}