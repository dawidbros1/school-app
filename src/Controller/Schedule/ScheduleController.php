<?php

namespace App\Controller\Schedule;

use App\Entity\Lesson\Lesson;
use App\Entity\Lesson\LessonTemplate;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\Schedule;
use App\Service\Entity\EntityProvider;
use App\Service\Form\FormErrors;
use App\Service\Form\Provider\LessonFormProvider;
use App\Service\Form\Provider\LessonTemplateFormProvider;
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
    * @Route("/show/{class_id}", name="app_class_schedule_show")
    */
   public function show(Request $request)
   {
      $class = $this->entityProvider->getSchoolClass($request->get('class_id'));

      $date = new \DateTime($request->get('date', 'now'));
      $dates = $this->getWeek($date->format("Y-m-d"));
      $lessonTimes = $this->em->getRepository(LessonTime::class)->findAll();
      $lessons = $this->em->getRepository(Lesson::class)->getIn($class, $dates);

      $nextDate = clone $date->modify("+7 days");
      $prevDate = clone $date->modify("-14 days");

      $schedules = [];

      foreach ($dates as $date) {
         $schedules[$date->format("Y-m-d")] = new Schedule();
      }

      foreach ($lessons as $lesson) {
         foreach ($dates as $date) {
            switch ($lesson->getDate()->format('Y-m-d')) {
               case $date->format("Y-m-d"):
                  $schedules[$date->format("Y-m-d")]->addLesson($lesson);
                  break;
            }
         }
      }

      return $this->render('schedule/show.html.twig', [
         'class' => $class,
         'schedules' => $schedules,
         'lessonTimes' => $lessonTimes,
         'nextDate' => $nextDate->format("Y-m-d"),
         'prevDate' => $prevDate->format("Y-m-d")
      ]);
   }

   /**
    * @Route("/manage/{class_id}/{date}", name="app_class_schedule_manage") 
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


      $schedule->sortBy($lessonTimes);

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
   public function fill(Request $request, FormErrors $formErrors, LessonTemplateFormProvider $lessonTemplateFormProvider): Response
   {
      $day = $request->get('day');
      $class = $this->entityProvider->getSchoolClass($request->get('class_id'));

      $form = $lessonTemplateFormProvider->getInitializerFormType($day, $class);
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

         foreach ($dates as $date) {
            foreach ($template as $item) {
               $lesson = new Lesson();
               $lesson->setClass($item->getClass());
               $lesson->setLessonTime($item->getLessonTime());
               $lesson->setTeacher($item->getTeacher());
               $lesson->setSubject($item->getSubject());
               $lesson->setPlace($item->getPlace());
               $lesson->setDate($date);

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

   private function getWeek($date)
   {
      $date = new \DateTime($date);
      $dates = [];

      while ($date->format("N") != 1) {
         $date->modify("-1 day");
      }

      while ($date->format("N") <= 5) {
         $dates[] = clone ($date);
         $date->modify("+1 day");
      }

      return $dates;
   }
}