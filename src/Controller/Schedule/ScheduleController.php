<?php

namespace App\Controller\Schedule;

use App\Entity\Schedule\ClassTime;
use App\Entity\Schedule\Schedule;
use App\Entity\SchoolClass\SchoolClass;
use App\Form\Schedule\InitializerFormType;
use App\Form\Schedule\ScheduleFormType;
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
 * @Route("/class/schedule")
 */
class ScheduleController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/manage/{class_id}/{date}", name="app_class_schedule_manage") 
    */
   public function manage(Request $request, FormBuilder $builder)
   {
      $date = new \DateTime($request->get('date'));
      $class = $this->getClass($request->get('class_id'));
      $schedule = $this->em->getRepository(Schedule::class)->findBy(['class' => $class, 'date' => $date]);

      $object = new Schedule();

      $form = $this->createForm(ScheduleFormType::class, $object, [
         'label' => "Dodaj lekcje",
         'action' => $this->generateUrl(
            "app_schedule_create",
            [
               'class_id' => $class->getId(),
               'date' => $date->format("Y-m-d")
            ]
         )
      ]);

      $builder->addButton("DODAJ")->build($form);

      return $this->render('schedule/manage.html.twig', [
         'form' => $form->createView(),
         'schedule' => $schedule,
         'class_id' => $class->getId()
      ]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/create/{class_id}/{date}", name="app_schedule_create")
    * @Method("POST")
    */
   public function create(Request $request, FormBuilder $builder, FormErrors $formErrors): Response
   {
      $date = new \DateTime($request->get('date'));
      $class = $this->getClass($request->get('class_id'));
      $schedule = new Schedule();

      $form = $this->createForm(ScheduleFormType::class, $schedule, [
         'label' => "Dodaj lekcje",
         'action' => $this->generateUrl(
            "app_schedule_create",
            [
               'class_id' => $class->getId(),
               'date' => $date->format("Y-m-d")
            ]
         )
      ]);

      $builder->addButton("Dodaj zajęcie")->build($form);
      $formErrors->load($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $schedule->setDate($date);
         $schedule->setClass($class);

         $repository = $this->em->getRepository(Schedule::class);
         $repository->add($schedule, true);

         $this->addFlash('success', "Lekcja została dodana");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_class_schedule_manage', ['class_id' => $class->getId(), 'date' => $date->format("Y-m-d")]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/edit/{id}", name="app_schedule_edit")
    */
   public function edit(Request $request, Schedule $object, FormBuilder $builder)
   {
      $schedule = $this->em->getRepository(Schedule::class)->findBy(['date' => $object->getDate(), 'class' => $object->getClass()]);

      $form = $this->createForm(ScheduleFormType::class, $object, [
         'label' => "Edycja lekcji"
      ]);

      $builder->addButton("ZAPISZ")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(Schedule::class);
         $repository->add($object, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");
         return $this->redirectToRoute('app_class_schedule_manage', ['date' => $object->getDate()->format("Y-m-d"), 'class_id' => $object->getClass()->getId()]);
      }

      return $this->render('schedule/manage.html.twig', [
         'form' => $form->createView(),
         'schedule' => $schedule,
         'class_id' => $object->getClass()->getId()
      ]);
   }


   /**
    * @Route("/show/{class_id}", name="app_class_schedule_show")
    */
   public function show(Request $request)
   {
      $class = $this->getClass($request->get("class_id"));
      $date = new \DateTime($request->get('date', 'now'));
      $dates = $this->getWeek($date->format("Y-m-d"));
      $schedule = $this->em->getRepository(Schedule::class)->getIn($class, $dates);
      $classTimes = $this->em->getRepository(ClassTime::class)->findAll();

      $nextDate = clone $date->modify("+7 days");
      $prevDate = clone $date->modify("-14 days");

      return $this->render('schedule/show.html.twig', [
         'schedule' => $schedule,
         'classTimes' => $classTimes,
         'dates' => $dates,
         'class_id' => $class->getId(),
         'nextDate' => $nextDate->format("Y-m-d"),
         'prevDate' => $prevDate->format("Y-m-d")
      ]);
   }

   /**
    * @Route("/delete/{id}", name="app_schedule_delete")
    * @Method("POST")
    */
   public function delete(Schedule $schedule)
   {
      $this->em->remove($schedule);
      $this->em->flush();
      $this->addFlash('success', "Zajęcie zostało usunięte");
      return $this->redirectToRoute('app_class_schedule_manage', ['class_id' => $schedule->getClass()->getId(), 'date' => $schedule->getDate()->format("Y-m-d")]);
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