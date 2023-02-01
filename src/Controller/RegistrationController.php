<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Roles;
use App\Entity\UserType\Admin;
use App\Entity\UserType\Student;
use App\Entity\UserType\Teacher;
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
    private $em;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/init/register/{type}", name="app_register_init")
     */
    public function initRegister(Request $request, EntityManagerInterface $entityManager, EmailGenerator $emailGenerator, UserCodeGenerator $codeGenerator): Response
    {
        // TODO: Jeżeli użytkownik ma rolę owner to wymagaj rangi ROLE_OWNER

        $user = $this->getUserEntity($request->get('type'));

        $form = $this->createForm(InitRegistrationFormType::class, $user, []);

        $form->add('submit', SubmitType::class, [
            'label' => "Dodaj konto",
            'attr' => [
                'class' => "btn btn-primary w-100 fw-bold",
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            'role' => $user->getRole()->getName()
        ]);
    }

    /**
     * @IsGranted("IS_ANONYMOUS")
     * @Route("/complete/register/{type}", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($request->query->has('pesel') && $request->query->has('code') && ($type = $request->get('type'))) {
            $pesel = $request->get('pesel');
            $code = $request->get('code');

            // TYPE CAN BE ONLY: student, teacher, admin

            $repository = $this->getUserRepository($type);
            $user = $repository->findOneBy(['pesel' => $pesel, 'code' => $code]);

            if ($user != null) {
                $form = $this->createForm(RegistrationFormType::class, $user, []);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid() && $this->passwordsAreIdentical($form)) {

                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );

                    $user->setCode(null);

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', "Konto zostało założone, możesz się na nie zalogować.");

                    return $this->redirectToRoute('app_login');
                }

                return $this->render('registration/register.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user
                ]);
            } else {
                $this->addFlash('error', "Dane autoryzacyjne są nie poprawne");
                return $this->redirectToRoute("app_register", ['type' => $type]);
            }
        }

        dump("ERROR: MISSING PARAMETERS");
        die();
    }

    private function passwordsAreIdentical($form)
    {
        if ($form->get('plainPassword')->getData() !== $form->get('passwordRepeat')->getData()) {
            $form->get('passwordRepeat')->addError(new FormError('Hasła nie są takie same'));
            return false;
        }

        return true;
    }

    private function getUserEntity($type)
    {
        switch ($type) {
            case 'student':
                return new Student();
            case 'teacher':
                return new Teacher();
            case 'admin':
                return new Admin();
        }

        // THROW EXCEPTION
    }

    private function getUserRepository($type)
    {
        switch ($type) {
            case 'student':
                return $this->em->getRepository(Student::class);
            case 'teacher':
                return $this->em->getRepository(Teacher::class);
            case 'admin':
                return $this->em->getRepository(Admin::class);
        }

        // THROW EXCEPTION
    }
}