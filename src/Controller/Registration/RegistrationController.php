<?php

namespace App\Controller\Registration;

use App\Enum\UserType;
use App\Form\Registration\RegistrationFormType;
use App\Service\User\EmailGenerator;
use App\Service\Form\FormBuilder;
use App\Service\User\UserCodeGenerator;
use App\Service\User\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Finder\Exception\AccessDeniedException;
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
    public function register(Request $request, EmailGenerator $emailGenerator, UserCodeGenerator $codeGenerator, AuthorizationCheckerInterface $authorizationChecker, FormBuilder $builder): Response
    {
        $userType = $request->get('UserType');

        if ($userType == UserType::ADMIN) {
            if (!$authorizationChecker->isGranted('ROLE_OWNER')) {
                throw new AccessDeniedException('Nie masz uprawnień do tworzenia użytkownika typu "admin".');
            }
        }

        $user = $this->userManager->getEntity($userType);
        $form = $this->createForm(RegistrationFormType::class, $user, []);
        $template = "base";

        if ($userType == UserType::STUDENT) {
            $template = $userType;
            $builder->addClassList();
        }

        $builder->addButton("Dodaj konto")->build($form);
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

        return $this->render('registration/init/' . $template . '.html.twig', [
            'form' => $form->createView(),
            'role' => $user->getRole()->getDescription()
        ]);
    }
}