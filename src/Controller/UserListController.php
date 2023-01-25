<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/list")
 */
class UserListController extends AbstractController
{
   private $repository;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->repository = $entityManager->getRepository(User::class);
   }

   /**
    * @IsGranted("ROLE_OWNER")
    * @Route("/admin", name="list_admin")
    */
   public function admin()
   {
      return $this->render('user/list.html.twig', [
         'users' => $this->repository->findAdmins()
      ]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/teacher", name="list_teacher")
    */
   public function teacher()
   {
      return $this->render('user/list.html.twig', [
         'users' => $this->repository->findTeachers()
      ]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/student", name="list_student")
    */
   public function student()
   {
      return $this->render('user/list.html.twig', [
         'users' => $this->repository->findStudents()
      ]);
   }
}