<?php

namespace App\Controller;

use App\Enum\UserType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractController
{
   private $simpleBoxes = [];
   private $complexBoxes = [];
   private $urlGenerator;
   private $userManager;

   public function __construct(UrlGeneratorInterface $urlGenerator, UserManager $userManager)
   {
      $this->urlGenerator = $urlGenerator;
      $this->userManager = $userManager;
   }

   /**
    * @IsGranted("ROLE_USER")
    * @Route("/dashboard", name="app_dashboard")
    */
   public function index()
   {
      // ONLY FOR LOGGED IN

      if ($this->isGranted('ROLE_OWNER')) {
         $this->ownerDashboard();
      } else if ($this->isGranted('ROLE_ADMIN')) {
         $this->adminDashboard();
      } else if ($this->isGranted('ROLE_TEACHER')) {
         $this->teacherDashboard();
      } else if ($this->isGranted('ROLE_STUDENT')) {
         $this->studentDashboard();
      }


      return $this->render('dashboard.html.twig', [
         'simpleBoxes' => $this->simpleBoxes,
         'complexBoxes' => $this->complexBoxes
      ]);
   }

   private function ownerDashboard()
   {
      $this->admins();
      $this->adminDashboard();
   }

   private function adminDashboard()
   {
      $this->teachers()->students();
      $this->classes()->listSubjects();
   }

   private function teacherDashboard()
   {
      $this->myClass();
   }

   private function studentDashboard()
   {
      $this->myClass();
   }

   private function createSimpleBox(array $data)
   {
      $obj = new \stdClass();
      $obj->title = $data['title'];
      $obj->description = $data['description'];
      $obj->url = $this->urlGenerator->generate($data['route'], $data['params'] ?? []);
      $obj->image = $data['image'];

      array_push($this->simpleBoxes, $obj);
      return $this;
   }

   private function createComplexBox(array $data)
   {
      $obj = new \stdClass();
      $obj->title = $data['title'];
      $obj->description = $data['description'];
      $obj->links = [];

      foreach ($data['links'] as $link) {
         $temp = new \stdClass();
         $temp->name = $link['name'];
         $temp->url = $link['route'] = $this->urlGenerator->generate($link['route'], $link['params'] ?? []);
         array_push($obj->links, $temp);
      }

      $obj->image = $data['image'];

      array_push($this->complexBoxes, $obj);
      return $this;
   }

   private function admins()
   {
      return $this->createComplexBox([
         'title' => "Administratorzy systemu",
         'description' => "Tutaj zobaczysz listę administratorow systemu",
         'links' => [
            [
               'name' => "Dodaj",
               'route' => "app_register",
               'params' => ["UserType" => UserType::ADMIN],
            ],
            [
               'name' => "Lista",
               'route' => "app_list_admin",
            ]
         ],
         'image' => "admin.png"
      ]);
   }

   private function teachers()
   {
      return $this->createComplexBox([
         'title' => "Nauczyciele",
         'description' => "Tutaj zobaczysz listę zatrudnionych nauczycieli",
         'links' => [
            [
               'name' => "Dodaj",
               'route' => "app_register",
               'params' => ["UserType" => UserType::TEACHER],
            ],
            [
               'name' => "Lista",
               'route' => "app_list_teacher",
            ]
         ],
         'image' => "teacher.png"
      ]);
   }

   private function students()
   {
      return $this->createComplexBox([
         'title' => "Uczniowie",
         'description' => "Tutaj zobaczysz listę uczniów",
         'links' => [
            [
               'name' => "Dodaj",
               'route' => "app_register",
               'params' => ["UserType" => UserType::STUDENT],
            ],
            [
               'name' => "Lista",
               'route' => "app_list_student",
            ]
         ],
         'image' => "student.png"
      ]);
   }

   # CLASS

   private function listClasses()
   {
      return $this->createSimpleBox([
         'title' => "Lista klas",
         'description' => "Tutaj wyświetlisz wszystkie klasy w szkole",
         'route' => "app_class_list",
         'image' => "class.png"
      ]);
   }

   private function addClass()
   {
      return $this->createSimpleBox([
         'title' => "Dodaj nową klasę",
         'description' => "Tutaj dodasz nową klasę",
         'route' => "app_class_create",
         'image' => "plus.png"
      ]);
   }

   private function myClass()
   {
      $user = $this->userManager->getUser(); // Student or Teacher has method getClass()

      if ($user->getClass() == null)
         return $this;
      else {
         return $this->createSimpleBox([
            'title' => "Moja klasa",
            'description' => "Tutaj zobaczysz swoją klasę",
            'route' => "app_my_class_show",
            'image' => "class.png"
         ]);
      }
   }

   private function listSubjects()
   {
      return $this->createSimpleBox([
         'title' => "Lista przedmiotów",
         'description' => "Tutaj zobaczysz listę przedmiotów",
         'route' => "app_subject_list",
         'image' => "books.png"
      ]);
   }
}