<?php

namespace App\Controller\Schedule;

use App\Entity\Schedule\ClassTime;
use App\Entity\Schedule\ScheduleTemplate;
use App\Form\Schedule\ClassTimeFormType;
use App\Service\FormBuilder;
use App\Service\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/schedule/classTime")
 */
class ClassTimeController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/list", name="app_classTime_list")
    */
   public function list(FormBuilder $builder, FormErrors $formErrors)
   {
      $classTime = new ClassTime();

      $form = $this->createForm(ClassTimeFormType::class, $classTime, [
         'label' => "Dodaj termin zajęć",
         'action' => $this->generateUrl("app_classTime_create")
      ]);

      $builder->addButton("DODAJ")->build($form);
      $formErrors->load($form);

      return $this->render('schedule/list.html.twig', [
         'classTimes' => $this->em->getRepository(ClassTime::class)->findAll(),
         'form' => $form->createView()
      ]);
   }

   /**
    * @Route("/create", name="app_classTime_create")
    * @Method("POST")
    */
   public function create(Request $request, FormErrors $formErrors, FormBuilder $builder)
   {
      $classTime = new ClassTime();

      $form = $this->createForm(ClassTimeFormType::class, $classTime, [
         'label' => "Dodaj termin zajęć",
         'action' => $this->generateUrl("app_classTime_create")
      ]);

      $builder->addButton("DODAJ")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(ClassTime::class);
         $repository->add($classTime, true);

         $this->addFlash('success', "Termin został utworzony");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_classTime_list');
   }

   /**
    * @Route("/edit/{id}", name="app_classTime_edit")
    */
   public function edit(Request $request, ClassTime $classTime, FormBuilder $builder)
   {
      $form = $this->createForm(ClassTimeFormType::class, $classTime, [
         'label' => "Edycja terminu zajęć"
      ]);

      $builder->addButton("ZAPISZ")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(ClassTime::class);
         $repository->add($classTime, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");
         return $this->redirectToRoute('app_classTime_list');
      }

      return $this->render('schedule/list.html.twig', [
         'classTimes' => $this->em->getRepository(ClassTime::class)->findAll(),
         'form' => $form->createView()
      ]);
   }

   /**
    * @Route("/delete/{id}", name="app_classTime_delete")
    * @Method("POST")
    */
   public function delete(ClassTime $classTime)
   {
      $scheduleTemplate = $this->em->getRepository(ScheduleTemplate::class)->findBy(['classTime' => $classTime]);

      if (empty($scheduleTemplate)) {
         $this->em->remove($classTime);
         $this->em->flush();
         $this->addFlash('success', "Termin został usunięty");
      } else {
         $this->addFlash('error', "Nie mozna usunąć tego terminu zajęć, ponieważ jest on używany w harmonogramie");
      }

      return $this->redirectToRoute('app_classTime_list');
   }
}