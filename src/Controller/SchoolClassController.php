<?php

namespace App\Controller;

use App\Entity\SchoolClass\SchoolClass;
use App\Entity\SchoolClass\SchoolClassStatus;
use App\Form\SchoolClassFormType;
use App\Service\FormBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
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
    * @IsGranted("ROLE_ADMIN")
    * @Route("/create", name="app_class_create")
    */
   public function create(Request $request, FormBuilder $builder): Response
   {
      $class = new SchoolClass();
      $form = $this->createForm(SchoolClassFormType::class, $class, []);
      $builder->addButton("Dodaj klasę")->build($form);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $repository = $this->em->getRepository(SchoolClassStatus::class);
         $class->setStatus($repository->findOneBy(['id' => SchoolClassStatus::ACTIVE]));
         $class->updateTeacherClass();

         $this->em->persist($class);
         $this->em->flush();

         $this->addFlash('success', "Klasa została utworzona");
         return $this->redirectToRoute('app_class_create');
      }

      return $this->render('schoolClass/admin/create.html.twig', [
         'form' => $form->createView()
      ]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/edit/{id}", name="app_class_edit")
    */
   public function edit(SchoolClass $class, Request $request, FormBuilder $builder)
   {
      $supervisingTeacher = $class->getTeacher();

      $form = $this->createForm(SchoolClassFormType::class, $class, []);
      $builder->addButton("Edytuj klasę")->build($form);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         if ($class->getTeacher() != $supervisingTeacher) {
            $supervisingTeacher != null ? $this->em->persist($supervisingTeacher->setClass(null)) : null;
            $class->updateTeacherClass();
         }

         $this->em->persist($class);
         $this->em->flush();

         $this->addFlash('success', "Dane klasy zostały zaaktualizowane");
         return $this->redirectToRoute('app_class_list');
      }

      return $this->render('schoolClass/admin/edit.html.twig', [
         'form' => $form->createView()
      ]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/list", name="app_class_list")
    */
   public function list(): Response
   {
      return $this->render('schoolClass/admin/list.html.twig', [
         'classes' => $this->em->getRepository(SchoolClass::class)->findALl()
      ]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/show/{id}", name="app_class_show")
    */
   public function show(SchoolClass $class): Response
   {
      return $this->render("schoolClass/admin/show.html.twig", [
         'class' => $class,
      ]);
   }

   /**
    * @IsGranted("ROLE_USER")
    * @Route("/show", name="app_my_class_show")
    */
   public function myClass()
   {
      if ($this->isGranted('ROLE_TEACHER')) {
         $folder = "teacher";
      } else if ($this->isGranted('ROLE_STUDENT')) {
         $folder = "student";
      } else {
         throw new AccessDeniedException("Niewłaściwy typ użytkownika");
      }

      $user = $this->getUser();

      if ($user->getClass() == null) {
         $this->addFlash('error', "Nie posiadasz przypisanej klasy");
         return $this->redirectToRoute('app_dashboard');
      }

      return $this->render("schoolClass/$folder/show.html.twig", [
         'class' => $user->getClass()
      ]);
   }
}