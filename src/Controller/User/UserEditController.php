<?php

namespace App\Controller\User;

use App\Enum\UserType;
use App\Form\Registration\RegistrationFormType;
use App\Service\User\EmailGenerator;
use App\Service\Form\FormBuilder;
use App\Service\User\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

# FOR ROLE_ADMIN AND ROLE_OWNER

class UserEditController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/{UserType}/edit/{id}", name="app_user_edit") 
    */
   public function edit(Request $request, EmailGenerator $emailGenerator, AuthorizationCheckerInterface $authorizationChecker, UserManager $userManager, FormBuilder $builder)
   {
      $userType = $request->get('UserType');

      if ($userType == UserType::ADMIN) {
         if (!$authorizationChecker->isGranted('ROLE_OWNER')) {
            throw new AccessDeniedException('Nie masz uprawnień do tworzenia użytkownika typu "admin".');
         }
      }

      $repository = $userManager->getRepository($userType);
      $user = $repository->findOneBy(['id' => $request->get('id')]);
      $form = $this->createForm(RegistrationFormType::class, $user, [
         'label' => "Autualizacja danych - " . $user->name()
      ]);

      if ($userType == UserType::STUDENT) {
         $builder->addClassList();
      } else if ($userType == UserType::TEACHER) {
         $builder->addClassListWithoutTutor($user);
      }

      $builder->addButton("Zapisz zmiany")->build($form);
      $oldClass = $userType == UserType::TEACHER ? $user->getClass() : null;
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $user->setEmail($emailGenerator->generate($user));

         if ($userType == UserType::TEACHER && $oldClass != $user->getClass()) {
            $oldClass != null ? $this->em->persist($oldClass->setTeacher(null)) : null;
            $user->updateClassTeacher();
         }

         $this->em->persist($user);
         $this->em->flush();

         $this->addFlash('success', "Dane zostały zaktualizowane");

         if ($to = $request->get("redirectTo", null)) {
            return $this->redirect($to);
         } else {
            return $this->redirectToRoute('app_list_' . $userType);
         }
      }

      return $this->render('user/edit/' . $userType . '.html.twig', [
         'form' => $form->createView(),
         'user' => $user,
      ]);
   }
}