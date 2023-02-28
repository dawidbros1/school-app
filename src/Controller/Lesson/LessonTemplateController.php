<?php

namespace App\Controller\Lesson;

use App\Entity\Lesson\LessonTemplate;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\ScheduleTemplate;
use App\Service\Entity\EntityProvider;
use App\Service\Form\FormBuilder;
use App\Service\Form\FormErrors;
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
 * @Route("/lesson/template")
 */
class LessonTemplateController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/create/{day}/{class_id}", name="app_lessonTemplate_create")
    * @Method("POST")
    */
   public function create(Request $request, LessonTemplateFormProvider $formProvider, FormErrors $formErrors, EntityProvider $entityProvider): Response
   {
      $day = $request->get('day');
      $class = $entityProvider->getSchoolClass($request->get('class_id'));
      $lesson = new LessonTemplate();

      $form = $formProvider->getCreateFormType($lesson, $class, $day);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(LessonTemplate::class);
         $lesson->setDay($day);
         $lesson->setClass($class);
         $repository->add($lesson, true);

         $this->addFlash('success', "Lekcja została dodana");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_scheduleTemplate_show', ['day' => $day, 'class_id' => $class->getId()]);
   }

   /**
    * @Route("/edit/{id}", name="app_lessonTemplate_edit")
    */
   public function edit(Request $request, LessonTemplate $lesson, FormBuilder $builder, LessonTemplateFormProvider $formProvider)
   {
      $lessonTimes = $this->em->getRepository(LessonTime::class)->findAll();

      $form = $formProvider->getEditFormType($lesson, $lesson->getClass(), $lesson->getDay(), [
         'lessonTimes' => $lessonTimes
      ]);

      $builder->addButton("Zapisz zmiany")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(LessonTemplate::class);
         $repository->add($lesson, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");
         return $this->redirectToRoute('app_scheduleTemplate_show', ['day' => $lesson->getDay(), 'class_id' => $lesson->getClass()->getId()]);
      }

      $schedule = new ScheduleTemplate($this->em->getRepository(LessonTemplate::class)->findBy(['day' => $lesson->getDay(), 'class' => $lesson->getClass()]));
      $schedule->sortBy($lessonTimes);

      return $this->render('schedule/template/show.html.twig', [
         'form' => $form->createView(),
         'initializerForm' => $formProvider->getInitializerFormType($lesson->getDay(), $lesson->getClass())->createView(),
         'schedule' => $schedule,
         'day' => $lesson->getDay(),
         'class_id' => $lesson->getClass()->getId(),
         'lessonTimes' => $lessonTimes
      ]);
   }

   /**
    * @Route("/delete/{id}", name="app_lessonTemplate_delete")
    * @Method("POST")
    */
   public function delete(LessonTemplate $lesson)
   {
      $this->em->remove($lesson);
      $this->em->flush();
      $this->addFlash('success', "Zajęcie zostało usunięte");
      return $this->redirectToRoute('app_scheduleTemplate_show', ['day' => $lesson->getDay(), 'class_id' => $lesson->getClass()->getId()]);
   }
}