<?php

namespace App\Controller;

use App\Entity\UserType\Admin;
use App\Entity\UserType\Student;
use App\Entity\UserType\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/list")
 */
class UserListController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @IsGranted("ROLE_OWNER")
    * @Route("/admin", name="list_admin")
    */
   public function admin()
   {
      return $this->render('user/list.html.twig', [
         'users' => $this->em->getRepository(Admin::class)->findAll()
      ]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/teacher", name="list_teacher")
    */
   public function teacher()
   {
      return $this->render('user/list.html.twig', [
         'users' => $this->em->getRepository(Teacher::class)->findAll()
      ]);
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/student", name="list_student")
    */
   public function student()
   {
      return $this->render('user/list.html.twig', [
         'users' => $this->em->getRepository(Student::class)->findAll()
      ]);
   }
}