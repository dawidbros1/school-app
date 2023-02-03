<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\RegistrationFormType;
use App\Service\EmailGenerator;
use App\Service\FormErrors;
use App\Service\FormPasswordManager;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserController extends AbstractController
{
   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/{UserType}/edit/{id}", name="app_user_edit") 
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

   /**
    * @IsGranted("ROLE_USER")
    * @Route("/user/profile", name="app_user_profile") 
    */
   public function profile(FormErrors $formErrors)
   {
      $user = $this->getUser();

      $form = $this->createForm(ChangePasswordFormType::class, $user, [
         'action' => $this->generateUrl("app_user_update_password")
      ]);

      $formErrors->load($form);

      return $this->render('user/profile.html.twig', [
         'form' => $form->createView(),
         'user' => $user,
      ]);
   }

   /**
    * @IsGranted("ROLE_USER")
    * @Route("/user/update/password", name="app_user_update_password") 
    */
   public function updatePassword(Request $request, UserManager $userManager, FormPasswordManager $passwordManager, FormErrors $formErrors)
   {
      $user = $this->getUser();
      $form = $this->createForm(ChangePasswordFormType::class, $user, []);
      $form->handleRequest($request);

      if ($form->isSubmitted()) {
         if ($form->isValid()& $passwordManager->passwordsAreIdentical($form)& $passwordManager->currentPasswordIsCorrect($user, $form)) {
            $user->setPassword($passwordManager->getHashedPassword($user, $form));
            $repository = $userManager->getRepository($user::getRole()->getName());
            $repository->add($user, true);

            $this->addFlash('success', "Hasło zostało zaktualizowane");
         } else {
            $formErrors->set($form);
         }
      }

      return $this->redirectToRoute('app_user_profile');
   }
}