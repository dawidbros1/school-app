<?php

namespace App\Controller\User;

use App\Form\RegistrationFormType;
use App\Service\EmailGenerator;
use App\Service\FormBuilder;
use App\Service\UserManager;
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

      if ($userType == "admin") {
         if (!$authorizationChecker->isGranted('ROLE_OWNER')) {
            throw new AccessDeniedException('Nie masz uprawnień do tworzenia użytkownika typu "admin".');
         }
      }

      $repository = $userManager->getRepository($userType);
      $user = $repository->findOneBy(['id' => $request->get('id')]);
      $form = $this->createForm(RegistrationFormType::class, $user, []);

      if ($userType == "student") {
         $builder->addClassList();
      } else if ($userType == "teacher") {
         $builder->addClassListWithoutTutor($user);
      }

      $builder->addButton("Zaktualizuj dane")->build($form);
      $oldClass = $userType == 'teacher' ? $user->getClass() : null;
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $user->setEmail($emailGenerator->generate($user));

         if ($userType == "teacher"  && $oldClass != $user->getClass()) {
            $oldClass != null ? $this->em->persist($oldClass->setTeacher(null)) : null;
            $user->updateClassTeacher();
         }

         $this->em->persist($user);
         $this->em->flush();

         $this->addFlash('success', "Dane zostały zaktualizowane");
         return $this->redirectToRoute('app_list_' . $userType);
      }

      return $this->render('user/edit/' . $userType . '.html.twig', [
         'form' => $form->createView(),
         'user' => $user,
      ]);
   }
}