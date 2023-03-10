<?php

namespace App\Controller;

use App\Entity\UserType\Student;
use App\Entity\UserType\Teacher;
use App\Enum\UserType;
use App\Interfaces\CustomUserInterface;
use App\Service\User\UserManager;
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
      $user = $this->userManager->getUser();

      if ($this->isGranted('ROLE_OWNER')) {
         $this->ownerDashboard();
      } else if ($this->isGranted('ROLE_ADMIN')) {
         $this->adminDashboard();
      } else if ($this->isGranted('ROLE_TEACHER')) {
         $this->teacherDashboard($user);
      } else if ($this->isGranted('ROLE_STUDENT')) {
         $this->studentDashboard($user);
      }

      return $this->render('dashboard.html.twig', [
         'simpleBoxes' => $this->simpleBoxes,
         'complexBoxes' => $this->complexBoxes
      ]);
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

   private function ownerDashboard()
   {
      $this->createComplexBox([
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

      $this->adminDashboard();
   }

   private function adminDashboard()
   {
      $this->createComplexBox([
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

      $this->createComplexBox([
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

      $this->createSimpleBox([
         'title' => "Lista klas",
         'description' => "Tutaj wyświetlisz wszystkie klasy w szkole",
         'route' => "app_class_list",
         'image' => "class.png"
      ]);

      $this->createSimpleBox([
         'title' => "Lista przedmiotów",
         'description' => "Tutaj zobaczysz listę przedmiotów",
         'route' => "app_subject_list",
         'image' => "books.png"
      ]);

      $this->createSimpleBox([
         'title' => "Rozkład zajęć",
         'description' => "Tutaj ustalisz godziny zajęć",
         'route' => "app_lessonTime_list",
         'image' => "clock.png"
      ]);
   }

   private function teacherDashboard(Teacher $user)
   {
      $this->addClassToBox($user, "app_teacher_class");

      $this->createSimpleBox([
         'title' => "Plan lekcji",
         'description' => "Tutaj wyświetlisz plan lekcji w wersji na duże ekrany",
         'route' => "app_teacher_schedule",
         'params' => ["device" => "desktop"],
         'image' => "desktop.png"
      ]);

      return $this->createSimpleBox([
         'title' => "Plan lekcji",
         'description' => "Tutaj wyświetlisz plan lekcji dostosowany do urządzenia mobilnego",
         'route' => "app_teacher_schedule",
         'params' => ["device" => "mobile"],
         'image' => "mobile.png"
      ]);
   }

   private function studentDashboard(Student $user)
   {
      $this->addClassToBox($user, "app_student_class");

      $this->createSimpleBox([
         'title' => "Plan lekcji",
         'description' => "Tutaj wyświetlisz plan lekcji w wersji na duże ekrany",
         'route' => "app_student_schedule",
         'params' => ["device" => "desktop"],
         'image' => "desktop.png"
      ]);

      return $this->createSimpleBox([
         'title' => "Plan lekcji (mobilny)",
         'description' => "Tutaj wyświetlisz plan lekcji dostosowany do urządzenia mobilnego",
         'route' => "app_student_schedule",
         'params' => ["device" => "mobile"],
         'image' => "mobile.png"
      ]);
   }

   // ===== //

   private function addClassToBox(CustomUserInterface $user, string $route)
   {
      if ($user->getClass() != null) {
         $this->createSimpleBox([
            'title' => "Moja klasa",
            'description' => "Tutaj zobaczysz swoją klasę",
            'route' => $route,
            'image' => "class.png"
         ]);
      }
   }
}