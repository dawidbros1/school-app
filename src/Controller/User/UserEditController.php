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

      if ($userType == 'teacher') {
         $oldClass = $user->getClass();
      }

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $user->setEmail($emailGenerator->generate($user));

         if ($userType == "teacher") {
            $this->editTeacher($user, $oldClass);
         }

         $repository->add($user, true);

         $this->addFlash('success', "Dane zostały zaktualizowane");

         return $this->redirectToRoute('app_list_' . $userType);
      }

      return $this->render('user/edit/' . $userType . '.html.twig', [
         'form' => $form->createView(),
         'user' => $user,
      ]);
   }

   private function editTeacher($user, $class_old)
   {
      $class_new = $user->getClass();

      if ($class_new != $class_old) {
         # NULL => NOT NULL         SET CLASS
         if ($class_old == null && $class_new != null) {
            $class_new->setTeacher($user); // class_new: add teacher
            $this->em->persist($class_new);
         }
         # NOT NULL => NOT NULL     CHANGE CLASS
         else if ($class_old != null && $class_new != null) {
            $class_old->setTeacher(null); // class_old: remove teacher
            $this->em->persist($class_old);

            $class_new->setTeacher($user); // class_new: add teacher
            $this->em->persist($class_old);
         }
         // # NOT NULL => NULL     REMOVE CLASS
         else if ($class_old != null && $class_new == null) {
            $class_old->setTeacher(null); // class_old: remove teacher
            $this->em->persist($class_old);
         }

         $this->em->flush();
      }
   }
}