<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Roles;
use App\Form\InitRegistrationFormType;
use App\Form\RegistrationFormType;
use App\Service\EmailGenerator;
use App\Service\UserCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class RegistrationController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/register/init/{role_name}", name="app_register_init")
     */
    public function initRegister(Request $request, EntityManagerInterface $entityManager, EmailGenerator $emailGenerator, UserCodeGenerator $codeGenerator): Response
    {
        // TODO: Jeżeli użytkownik ma rolę owner to wymagaj rangi ROLE_OWNER

        $user = new User();
        $role = $entityManager->getRepository(Roles::class)->findOneBy(['name' => $request->get('role_name')]);

        if ($role == null) {
            dump("Rola nie istnieje");
            exit();
        }

        $form = $this->createForm(InitRegistrationFormType::class, $user, []);

        $form->add('submit', SubmitType::class, [
            'label' => "Dodaj konto",
            'attr' => [
                'class' => "btn btn-primary w-100 fw-bold",
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles([$role->getName()]);
            $user->setEmail($emailGenerator->generate($user));
            $user->setCode($codeGenerator->generate($user));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "Konto zostało zainicjowane, oczekuje na dokończenie rejestracji.");

            if ($referer = $request->headers->get('referer')) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_dashboard');
            }
        }

        return $this->render('registration/initRegistration.html.twig', [
            'form' => $form->createView(),
            'role' => $role
        ]);
    }

    private function passwordsAreIdentical($form)
    {
        if ($form->get('plainPassword')->getData() !== $form->get('passwordRepeat')->getData()) {
            $form->get('passwordRepeat')->addError(new FormError('Hasła nie są takie same'));
            return false;
        }

        return true;
    }
}