<?php

namespace App\Controller;

use App\Form\CompleteRegistrationFormType;
use App\Form\RegistrationFormType;
use App\Service\EmailGenerator;
use App\Service\FormPasswordManager;
use App\Service\UserCodeGenerator;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RegistrationController extends AbstractController
{
    private $em;
    private $userManager;
    public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->em = $entityManager;
        $this->userManager = $userManager;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/register/{UserType}", name="app_register")
     */
    public function register(Request $request, EmailGenerator $emailGenerator, UserCodeGenerator $codeGenerator, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $userType = $request->get('UserType');

        if ($userType == "admin") {
            if (!$authorizationChecker->isGranted('ROLE_OWNER')) {
                throw new AccessDeniedException('Nie masz uprawnień do tworzenia użytkownika typu "admin".');
            }
        }

        $user = $this->userManager->getEntity($userType);
        $form = $this->createForm(RegistrationFormType::class, $user, []);

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

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "Konto zostało zainicjowane, oczekuje na dokończenie rejestracji przez użytkownika.");

            if ($referer = $request->headers->get('referer')) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_dashboard');
            }
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'role' => $user->getRole()->getDescription()
        ]);
    }

    /**
     * @IsGranted("IS_ANONYMOUS")
     * @Route("/complete/register/{UserType}", name="app_complete_register")
     */
    public function completeRegister(Request $request, FormPasswordManager $passwordManager): Response
    {
        if ($request->query->has('pesel') && $request->query->has('code') && ($type = $request->get('UserType'))) {
            $pesel = $request->get('pesel');
            $code = $request->get('code');

            $repository = $this->userManager->getRepository($type);
            $user = $repository->findOneBy(['pesel' => $pesel, 'code' => $code]);

            if ($user != null) {
                $form = $this->createForm(CompleteRegistrationFormType::class, $user, []);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid() && $passwordManager->passwordsAreIdentical($form)) {
                    $user->setPassword($passwordManager->getHashedPassword($user, $form));
                    $user->setCode(null);

                    $this->em->persist($user);
                    $this->em->flush();

                    $this->addFlash('success', "Konto zostało założone, możesz się na nie zalogować.");
                    return $this->redirectToRoute('app_login', ['email' => $user->getEmail()]);
                }

                return $this->render('registration/completeRegister.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user
                ]);
            } else {
                $this->addFlash('error', "Błąd autoryzacji: nie udało się zidentyfikować użytkownika.");
                return $this->redirectToRoute("app_home");
            }
        } else {
            $this->addFlash('error', "Niepoprawny adres: link zawiera brakujące paremetry.");
            return $this->redirectToRoute("app_home");
        }
    }
}