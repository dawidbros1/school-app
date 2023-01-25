<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Roles;
use App\Form\InitRegistrationFormType;
use App\Service\EmailGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/edit/{id}", name="app_user_edit")
    */
   public function edit(User $user, Request $request, EntityManagerInterface $entityManager, EmailGenerator $emailGenerator)
   {
      // TODO: Jeżeli użytkownik ma rolę owner to wymagaj rangi ROLE_OWNER

      $form = $this->createForm(InitRegistrationFormType::class, $user, []);

      $form->add('submit', SubmitType::class, [
         'label' => "Zaktualizuj dane",
         'attr' => [
            'class' => "btn btn-primary w-100 fw-bold",
         ],
      ]);

      $form->handleRequest($request);

      $role = $entityManager->getRepository(Roles::class)->findOneBy(['name' => $user->getRoles()[0]]);

      if ($form->isSubmitted() && $form->isValid()) {

         $user->setEmail($emailGenerator->generate($user));
         $entityManager->persist($user);
         $entityManager->flush();

         $this->addFlash('success', "Dane zostały zaktualizowane");

         return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
      }

      return $this->render('user/edit.html.twig', [
         'form' => $form->createView(),
         'user' => $user,
         'role' => $role
      ]);
   }
}