<?php

namespace App\Controller;

use App\Entity\SchoolClass\SchoolClass;
use App\Entity\SchoolClass\SchoolClassStatus;
use App\Form\SchoolClassFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/class")
 */
class SchoolClassController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @Route("/create", name="app_class_create")
    */
   public function create(Request $request): Response
   {
      $class = new SchoolClass();
      $form = $this->createForm(SchoolClassFormType::class, $class, []);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(SchoolClassStatus::class);
         $class->setStatus($repository->findOneBy(['id' => SchoolClassStatus::ACTIVE]));

         if (($teacher = $class->getTeacher()) != null) {
            $teacher->setClass($class);
            $this->em->persist($teacher);
         }

         $this->em->persist($class);
         $this->em->flush();

         $this->addFlash('success', "Klasa została utworzona");
         return $this->redirectToRoute('app_class_create');
      }

      return $this->render('schoolClass/create.html.twig', [
         'form' => $form->createView()
      ]);
   }

   /**
    * @Route("/edit/{id}", name="app_class_edit")
    */
   public function edit(SchoolClass $class, Request $request)
   {
      $supervisingTeacher = $class->getTeacher(); // old

      $form = $this->createForm(SchoolClassFormType::class, $class, []);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $teacher = $class->getTeacher(); // new

         if ($teacher != $supervisingTeacher) {
            # NULL => NOT NULL         INIT SUPERVISING TEACHER
            if ($supervisingTeacher == null && $teacher != null) {
               $teacher->setClass($class);
               $this->em->persist($teacher);
            }
            # NOT NULL => NOT NULL     SET NEW SUPERVISING TEACHER
            else if ($supervisingTeacher != null && $teacher != null) {
               $supervisingTeacher->setClass(null);
               $this->em->persist($supervisingTeacher);

               $teacher->setClass($class);
               $this->em->persist($teacher);
            }
            # NOT NULL => NULL         REMOVE SUPERVISING TEACHER
            else if ($supervisingTeacher != null && $teacher == null) {
               $supervisingTeacher->setClass(null);
               $this->em->persist($supervisingTeacher);
            }

            $class->setTeacher($teacher);
            $this->em->persist($class);
         }

         $this->em->flush();

         $this->addFlash('success', "Dane klasy zostały zaaktualizowane");
         return $this->redirectToRoute('app_class_edit', ['id' => $class->getId()]);
      }

      return $this->render('schoolClass/edit.html.twig', [
         'form' => $form->createView()
      ]);
   }

   /**
    * @Route("/list", name="app_class_list")
    */
   public function list(): Response
   {
      return $this->render('schoolClass/list.html.twig', [
         'classes' => $this->em->getRepository(SchoolClass::class)->findALl()
      ]);
   }
}