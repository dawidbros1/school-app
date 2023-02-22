<?php

namespace App\Controller\Schedule;

use App\Entity\Schedule\ScheduleTemplate;
use App\Entity\SchoolClass\SchoolClass;
use App\Form\Schedule\InitializerFormType;
use App\Form\Schedule\ScheduleTemplateFormType;
use App\Service\FormBuilder;
use App\Service\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
   public function show(Request $request, FormBuilder $builder, FormErrors $formErrors)
   {
      $day = $request->get('day');
      $class = $this->getClass($request->get('class_id'));
      $scheduleTemplate = new ScheduleTemplate();

      $schedule = $this->em->getRepository(ScheduleTemplate::class)->findBy(['day' => $day, 'class' => $class]);

      $form = $this->createForm(ScheduleTemplateFormType::class, $scheduleTemplate, [
         'label' => "Dodaj lekcje",
         'action' => $this->generateUrl(
            "app_scheduleTemplate_create",
            [
               'class_id' => $class->getId(),
               'day' => $day
            ]
         )
      ]);

      $builder->addButton("DODAJ")->build($form);

      return $this->render('schedule/template.html.twig', [
         'form' => $form->createView(),
         'initializerForm' => $this->getInitializerFormType($day, $class),
         'schedule' => $schedule,
         'day' => $day,
         'class_id' => $class->getId()
      ]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/create/{day}/{class_id}", name="app_scheduleTemplate_create")
    * @Method("POST")
    */
   public function create(Request $request, FormBuilder $builder, FormErrors $formErrors): Response
   {
      $day = $request->get('day');
      $class = $this->getClass($request->get('class_id'));
      $scheduleTemplate = new ScheduleTemplate();

      $form = $this->createForm(ScheduleTemplateFormType::class, $scheduleTemplate, [
         'label' => "Dodaj lekcje",
         'action' => $this->generateUrl(
            "app_scheduleTemplate_create",
            [
               'class_id' => $class->getId(),
               'day' => $day
            ]
         )
      ]);

      $builder->addButton("Dodaj klasę")->build($form);
      $formErrors->load($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(ScheduleTemplate::class);
         $scheduleTemplate->setDay($day);
         $scheduleTemplate->setClass($class);
         $repository->add($scheduleTemplate, true);

         $this->addFlash('success', "Lekcja została dodana");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_scheduleTemplate_show', ['day' => $day, 'class_id' => $class->getId()]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/edit/{id}", name="app_scheduleTemplate_edit")
    */
   public function edit(Request $request, ScheduleTemplate $scheduleTemplate, FormBuilder $builder)
   {
      $schedule = $this->em->getRepository(ScheduleTemplate::class)->findBy(['day' => $scheduleTemplate->getDay(), 'class' => $scheduleTemplate->getClass()]);

      $form = $this->createForm(ScheduleTemplateFormType::class, $scheduleTemplate, [
         'label' => "Edycja lekcji"
      ]);

      $builder->addButton("ZAPISZ")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(ScheduleTemplate::class);
         $repository->add($scheduleTemplate, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");
         return $this->redirectToRoute('app_scheduleTemplate_show', ['day' => $scheduleTemplate->getDay(), 'class_id' => $scheduleTemplate->getClass()->getId()]);
      }

      return $this->render('schedule/template.html.twig', [
         'form' => $form->createView(),
         'initializerForm' => $this->getInitializerFormType($scheduleTemplate->getDay(), $scheduleTemplate->getClass()),
         'schedule' => $schedule,
         'day' => $scheduleTemplate->getDay(),
         'class_id' => $scheduleTemplate->getClass()->getId()
      ]);
   }

   /**
    * @Route("/delete/{id}", name="app_scheduleTemplate_delete")
    * @Method("POST")
    */
   public function delete(ScheduleTemplate $scheduleTemplate)
   {
      $this->em->remove($scheduleTemplate);
      $this->em->flush();
      $this->addFlash('success', "Zajęcie zostało usunięte");
      return $this->redirectToRoute('app_scheduleTemplate_show', ['day' => $scheduleTemplate->getDay(), 'class_id' => $scheduleTemplate->getClass()->getId()]);
   }


   private function getClass($id)
   {
      $class = $this->em->getRepository(SchoolClass::class)->findOneBy(['id' => $id]);

      if (!$class) {
         dump("klasa nie istnieje");
         die();
      }

      return $class;
   }

   private function getInitializerFormType($day, $class)
   {
      $form = $this->createForm(InitializerFormType::class, null, [
         'action' => $this->generateUrl("app_schedule_fill", ['day' => $day, 'class_id' => $class->getId()])
      ]);

      return $form->createView();
   }
}