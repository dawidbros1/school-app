<?php

namespace App\Controller\User;

use App\Form\ChangePasswordFormType;
use App\Service\FormErrors;
use App\Service\FormPasswordManager;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
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