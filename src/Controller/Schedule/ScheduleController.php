<?php

namespace App\Controller\Schedule;

use App\Entity\Lesson\Lesson;
use App\Entity\Lesson\LessonTemplate;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\Schedule;
use App\Service\Entity\EntityProvider;
use App\Service\Form\FormProvider;
use App\Service\Form\FormErrors;
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
   private $formProvider;
   public function __construct(EntityManagerInterface $entityManager, EntityProvider $entityProvider, FormProvider $formProvider)
   {
      $this->em = $entityManager;
      $this->entityProvider = $entityProvider;
      $this->formProvider = $formProvider;
   }

   /**
    * @Route("/manage/{class_id}/{date}", name="app_class_schedule_manage") 
    */
   public function manage(Request $request)
   {
      $date = new \DateTime($request->get('date'));
      $today = new \DateTime('now');

      if ($today->modify("-1 day") > $date) {
         die("TODO: ScheduleController:manage");
      }

      $lesson = new Lesson();
      $class = $this->entityProvider->getSchoolClass($request->get('class_id'));
      $schedule = $this->em->getRepository(Lesson::class)->findBy(['class' => $class, 'date' => $date]);

      return $this->render('schedule/manage.html.twig', [
         'form' => $this->formProvider->getLessonFormType($lesson, $class, $date, "Dodaj lekcje")->createView(),
         'schedule' => $schedule,
         'class_id' => $class->getId()
      ]);
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
         'schedules' => $schedules,
         'lessonTimes' => $lessonTimes,
         'class_id' => $class->getId(),
         'nextDate' => $nextDate->format("Y-m-d"),
         'prevDate' => $prevDate->format("Y-m-d")
      ]);
   }

   /**
    * @Route("/fill/{class_id}/{day}", name="app_schedule_fill")
    * @Method("POST")
    */
   public function fill(Request $request, FormErrors $formErrors): Response
   {
      $day = $request->get('day');
      $class = $this->entityProvider->getSchoolClass($request->get('class_id'));

      $form = $this->formProvider->getInitializerFormType($day, $class);
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
         $this->addFlash('success', "Harmonogram został utworzony");
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