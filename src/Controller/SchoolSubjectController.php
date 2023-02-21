<?php

namespace App\Controller;

use App\Entity\Schedule\ScheduleTemplate;
use App\Entity\SchoolSubject;
use App\Enum\SchoolSubjectStatus;
use App\Form\SchoolSubjectFormType;
use App\Service\FormBuilder;
use App\Service\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
   public function list(Request $request, FormBuilder $builder, FormErrors $formErrors)
   {
      $subject = new SchoolSubject();

      $form = $this->createForm(SchoolSubjectFormType::class, $subject, [
         'label' => "Tworzenie przedmiotu",
         'action' => $this->generateUrl("app_subject_create")
      ]);

      $formErrors->load($form);
      $builder->addButton("Dodaj przedmiot")->build($form);

      return $this->render('schoolSubject/list.html.twig', [
         'subjects' => $this->em->getRepository(SchoolSubject::class)->findAll(),
         'form' => $form->createView()
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
         'label' => "Edycja przedmiotu"
      ]);

      $builder->addButton("Edytuj przedmiot")->build($form);
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
      ]);
   }

   /**
    * @Route("/delete/{id}", name="app_subject_delete")
    * @Method("POST")
    */
   public function delete(SchoolSubject $subject)
   {
      $scheduleTemplate = $this->em->getRepository(ScheduleTemplate::class)->findBy(['subject' => $subject]);

      if (empty($scheduleTemplate)) {
         $this->em->remove($subject);
         $this->em->flush();
         $this->addFlash('success', "Przedmiot [ " . $subject->getName() . " ] zostały usunięty");
      } else {
         $this->addFlash('error', "Nie mozna usunąć tego przedmiotu, ponieważ jest on używany w harmonogramie");
      }

      return $this->redirectToRoute('app_subject_list');
   }
}