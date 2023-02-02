<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Service\EmailGenerator;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/{UserType}")
 */
class UserController extends AbstractController
{
   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/edit/{id}", name="app_user_edit") 
    */
   public function edit(Request $request, EmailGenerator $emailGenerator, AuthorizationCheckerInterface $authorizationChecker, UserManager $userManager)
   {
      $userType = $request->get('UserType');

      if ($userType == "admin") {
         if (!$authorizationChecker->isGranted('ROLE_OWNER')) {
            throw new AccessDeniedException('Nie masz uprawnień do tworzenia użytkownika typu "admin".');
         }
      }

      $repository = $userManager->getRepository($userType);

      $user = $repository->findOneBy(['id' => $request->get('id')]);
      $form = $this->createForm(RegistrationFormType::class, $user, []);

      $form->add('submit', SubmitType::class, [
         'label' => "Zaktualizuj dane",
         'attr' => [
            'class' => "btn btn-primary w-100 fw-bold",
         ],
      ]);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

         $user->setEmail($emailGenerator->generate($user));
         $repository->add($user, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");

         return $this->redirectToRoute('app_user_edit', ['id' => $user->getId(), "UserType" => $userType]);
      }

      return $this->render('user/edit.html.twig', [
         'form' => $form->createView(),
         'user' => $user,
      ]);
   }
}