<?php

namespace App\Controller\Lesson;

use App\Entity\Lesson\LessonTemplate;
use App\Entity\Lesson\LessonTime;
use App\Form\Lesson\LessonTimeFormType;
use App\Service\Form\FormBuilder;
use App\Service\Form\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/schedule/lessonTime")
 */
class LessonTimeController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/list", name="app_lessonTime_list")
    */
   public function list(FormBuilder $builder, FormErrors $formErrors)
   {
      $lessonTime = new LessonTime();

      $form = $this->createForm(LessonTimeFormType::class, $lessonTime, [
         'label' => "Tworzenie terminu zajęć",
         'action' => $this->generateUrl("app_lessonTime_create")
      ]);

      $builder->addButton("Dodaj termin")->build($form);
      $formErrors->load($form);

      return $this->render('lesson/time/show.html.twig', [
         'lessonTimes' => $this->em->getRepository(LessonTime::class)->findAll(),
         'form' => $form->createView(),
         'type' => "create"
      ]);
   }

   /**
    * @Route("/create", name="app_lessonTime_create")
    * @Method("POST")
    */
   public function create(Request $request, FormErrors $formErrors, FormBuilder $builder)
   {
      $lessonTime = new LessonTime();

      $form = $this->createForm(LessonTimeFormType::class, $lessonTime, [
         'label' => "Dodaj termin zajęć",
         'action' => $this->generateUrl("app_lessonTime_create")
      ]);

      $builder->addButton("DODAJ")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(LessonTime::class);
         $repository->add($lessonTime, true);

         $this->addFlash('success', "Termin został utworzony");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_lessonTime_list');
   }

   /**
    * @Route("/edit/{id}", name="app_lessonTime_edit")
    */
   public function edit(Request $request, LessonTime $lessonTime, FormBuilder $builder)
   {
      $form = $this->createForm(LessonTimeFormType::class, $lessonTime, [
         'label' => $lessonTime->time()
      ]);

      $builder->addButton("Zapisz zmiany")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(LessonTime::class);
         $repository->add($lessonTime, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");
         return $this->redirectToRoute('app_lessonTime_list');
      }

      return $this->render('lesson/time/show.html.twig', [
         'lessonTimes' => $this->em->getRepository(LessonTime::class)->findAll(),
         'form' => $form->createView(),
         'type' => "edit"
      ]);
   }

   /**
    * @Route("/delete/{id}", name="app_lessonTime_delete")
    * @Method("POST")
    */
   public function delete(LessonTime $lessonTime)
   {
      $lessons = $this->em->getRepository(LessonTemplate::class)->findBy(['lessonTime' => $lessonTime]);

      if (empty($lessons)) {
         $this->em->remove($lessonTime);
         $this->em->flush();
         $this->addFlash('success', "Termin został usunięty");
      } else {
         $this->addFlash('error', "Nie mozna usunąć tego terminu zajęć, ponieważ jest on używany w harmonogramie");
      }

      return $this->redirectToRoute('app_lessonTime_list');
   }
}