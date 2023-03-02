<?php

namespace App\Controller\Lesson;

use App\Entity\Lesson\Lesson;
use App\Entity\Lesson\LessonStatus;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\Schedule;
use App\Service\Entity\EntityProvider;
use App\Service\Form\FormErrors;
use App\Service\Form\Provider\LessonFormProvider;
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
class LessonController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/create/{class_id}/{date}", name="app_lesson_create")
    * @Method("POST")
    */
   public function create(Request $request, LessonFormProvider $formProvider, FormErrors $formErrors, EntityProvider $entityProvider): Response
   {
      $lesson = new Lesson();
      $date = new \DateTime($request->get('date'));
      $class = $entityProvider->getSchoolClass($request->get('class_id'));

      $form = $formProvider->getCreateFormType($lesson, $class, $date, []);
      $formErrors->load($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $status = $this->em->getRepository(LessonStatus::class)->findOneBy(['id' => 4]); // 4 => new

         $lesson->setDate($date);
         $lesson->setClass($class);
         $lesson->setLessonStatus($status);

         $repository = $this->em->getRepository(Lesson::class);
         $repository->add($lesson, true);

         $this->addFlash('success', "Lekcja została dodana");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_schedule_manage', ['class_id' => $class->getId(), 'date' => $date->format("Y-m-d")]);
   }

   /**
    * @Route("/edit/{id}", name="app_lesson_edit")
    */
   public function edit(Request $request, Lesson $lesson, LessonFormProvider $formProvider)
   {
      $form = $formProvider->getEditFormType($lesson);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(Lesson::class);
         $repository->add($lesson, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");
         return $this->redirectToRoute('app_schedule_manage', ['date' => $lesson->getDate()->format("Y-m-d"), 'class_id' => $lesson->getClass()->getId()]);
      }

      $schedule = new Schedule($this->em->getRepository(Lesson::class)->findBy(['date' => $lesson->getDate(), 'class' => $lesson->getClass()]));
      $lessonTimes = $this->em->getRepository(LessonTime::class)->findAll();
      $schedule->include($lessonTimes);

      return $this->render('schedule/manage.html.twig', [
         'form' => $form->createView(),
         'schedule' => $schedule,
         'class' => $lesson->getClass(),
         'date' => $lesson->getDate()->format("Y-m-d"),
         'lessonTimes' => $lessonTimes
      ]);
   }

   /**
    * @Route("/delete/{id}", name="app_lesson_delete")
    * @Method("POST")
    */
   public function delete(Lesson $lesson)
   {
      $this->em->remove($lesson);
      $this->em->flush();
      $this->addFlash('success', "Zajęcie zostało usunięte");
      return $this->redirectToRoute('app_schedule_manage', ['class_id' => $lesson->getClass()->getId(), 'date' => $lesson->getDate()->format("Y-m-d")]);
   }
}