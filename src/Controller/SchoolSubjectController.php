<?php

namespace App\Controller;

use App\Entity\Lesson\LessonTemplate;
use App\Entity\SchoolSubject;
use App\Enum\SchoolSubjectStatus;
use App\Form\SchoolSubjectFormType;
use App\Service\Form\FormBuilder;
use App\Service\Form\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/subject")
 */
class SchoolSubjectController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/list", name="app_subject_list")
    */
   public function list(FormBuilder $builder, FormErrors $formErrors)
   {
      $subject = new SchoolSubject();
      $form = $this->createForm(SchoolSubjectFormType::class, $subject, [
         'label' => "Tworzenie przedmiotu",
         'action' => $this->generateUrl("app_subject_create")
      ]);

      $formErrors->load($form);
      $builder->addButton("Dodaj przedmiot")->build($form);

      return $this->render('schoolSubject/list.html.twig', [
         'form' => $form->createView(),
         'subjects' => $this->em->getRepository(SchoolSubject::class)->findAll(),
         'type' => "create"
      ]);
   }

   /**
    * @Route("/create", name="app_subject_create")
    * @Method("POST")
    */
   public function create(Request $request, FormErrors $formErrors, FormBuilder $builder)
   {
      $subject = new SchoolSubject();

      $form = $this->createForm(SchoolSubjectFormType::class, $subject, []);
      $builder->addButton("Dodaj przedmiot")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(SchoolSubject::class);
         $subject->setStatus(SchoolSubjectStatus::ACTIVE);
         $repository->add($subject, true);

         $this->addFlash('success', "Przedmiot została utworzony");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_subject_list');
   }

   /**
    * @Route("/edit/{id}", name="app_subject_edit")
    */
   public function edit(Request $request, SchoolSubject $subject, FormBuilder $builder)
   {
      $form = $this->createForm(SchoolSubjectFormType::class, $subject, [
         'label' => $subject->getName()
      ]);

      $builder->addButton("Zapisz zmiany")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(SchoolSubject::class);
         $repository->add($subject, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");
         return $this->redirectToRoute('app_subject_list');
      }

      return $this->render('schoolSubject/list.html.twig', [
         'subjects' => $this->em->getRepository(SchoolSubject::class)->findAll(),
         'form' => $form->createView(),
         'type' => "edit"
      ]);
   }

   /**
    * @Route("/delete/{id}", name="app_subject_delete")
    * @Method("POST")
    */
   public function delete(SchoolSubject $subject)
   {
      $lessons = $this->em->getRepository(LessonTemplate::class)->findBy(['subject' => $subject]);

      if (empty($lessons)) {
         $this->em->remove($subject);
         $this->em->flush();
         $this->addFlash('success', "Przedmiot [ " . $subject->getName() . " ] zostały usunięty");
      } else {
         $this->addFlash('error', "Nie mozna usunąć tego przedmiotu, ponieważ jest on używany w harmonogramie");
      }

      return $this->redirectToRoute('app_subject_list');
   }
}