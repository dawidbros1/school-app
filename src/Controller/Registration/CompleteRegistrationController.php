<?php

namespace App\Controller\Registration;

use App\Form\Registration\CompleteRegistrationFormType;
use App\Service\Form\FormPasswordManager;
use App\Service\User\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CompleteRegistrationController extends AbstractController
{
    private $em;
    private $userManager;
    public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
    {
        $this->em = $entityManager;
        $this->userManager = $userManager;
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

                return $this->render('registration/complete/register.html.twig', [
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