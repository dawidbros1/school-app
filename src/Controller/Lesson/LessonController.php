<?php

namespace App\Controller\Lesson;

use App\Entity\Lesson\Lesson;
use App\Form\Lesson\LessonFormType;
use App\Service\Entity\EntityProvider;
use App\Service\Form\FormProvider;
use App\Service\Form\FormBuilder;
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
class LessonController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/create/{class_id}/{date}", name="app_schedule_create")
    * @Method("POST")
    */
   public function create(Request $request, FormProvider $formProvider, FormErrors $formErrors, EntityProvider $entityProvider): Response
   {
      $date = new \DateTime($request->get('date'));
      $class = $entityProvider->getSchoolClass($request->get('class_id'));
      $lesson = new Lesson();

      $form = $formProvider->getLessonFormType($lesson, $class, $date, "Dodaj zajęcie");
      $formErrors->load($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $lesson->setDate($date);
         $lesson->setClass($class);

         $repository = $this->em->getRepository(Lesson::class);
         $repository->add($lesson, true);

         $this->addFlash('success', "Lekcja została dodana");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_class_schedule_manage', ['class_id' => $class->getId(), 'date' => $date->format("Y-m-d")]);
   }

   /**
    * @Route("/edit/{id}", name="app_schedule_edit")
    */
   public function edit(Request $request, Lesson $lesson, FormBuilder $builder)
   {
      $schedule = $this->em->getRepository(Lesson::class)->findBy(['date' => $lesson->getDate(), 'class' => $lesson->getClass()]);

      $form = $this->createForm(LessonFormType::class, $lesson, [
         'label' => "Edycja lekcji"
      ]);

      $builder->addButton("ZAPISZ")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(Lesson::class);
         $repository->add($lesson, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");
         return $this->redirectToRoute('app_class_schedule_manage', ['date' => $lesson->getDate()->format("Y-m-d"), 'class_id' => $lesson->getClass()->getId()]);
      }

      return $this->render('schedule/manage.html.twig', [
         'form' => $form->createView(),
         'schedule' => $schedule,
         'class_id' => $lesson->getClass()->getId()
      ]);
   }

   /**
    * @Route("/delete/{id}", name="app_schedule_delete")
    * @Method("POST")
    */
   public function delete(Lesson $lesson)
   {
      $this->em->remove($lesson);
      $this->em->flush();
      $this->addFlash('success', "Zajęcie zostało usunięte");
      return $this->redirectToRoute('app_class_schedule_manage', ['class_id' => $lesson->getClass()->getId(), 'date' => $lesson->getDate()->format("Y-m-d")]);
   }
}