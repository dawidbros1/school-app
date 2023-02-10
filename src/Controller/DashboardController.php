<?php

namespace App\Controller;

use App\Enum\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractController
{
   private $boxes;
   private $urlGenerator;

   public function __construct(UrlGeneratorInterface $urlGenerator)
   {
      $this->boxes = [];
      $this->urlGenerator = $urlGenerator;
   }

   /**
    * @IsGranted("ROLE_USER")
    * @Route("/dashboard", name="app_dashboard")
    */
   public function index()
   {
      // ONLY FOR LOGGED IN

      if ($this->isGranted('ROLE_OWNER')) {
         $this->owner();
      } else if ($this->isGranted('ROLE_ADMIN')) {
         $this->admin();
      } else if ($this->isGranted('ROLE_TEACHER')) {
         $this->teacher();
      } else if ($this->isGranted('ROLE_STUDENT')) {
         $this->student();
      }

      return $this->render('dashboard.html.twig', [
         'boxes' => $this->boxes
      ]);
   }

   private function owner()
   {
      $this->listAdmins()->listTeacher()->listStudent();
      $this->addAdmin()->addTeacher()->addStudent();
      $this->listClasses()->addClass();
   }

   private function admin()
   {
      $this->listTeacher()->listStudent();
      $this->addTeacher()->addStudent();
      $this->listClasses()->addClass();
   }

   private function teacher()
   {
      $this->myClass();
   }

   private function student()
   {
      //  Może wyświetlać własną klasę
   }

   // LIST OF AVAIABLE BOXES

   private function create(array $data)
   {
      $obj = new \stdClass();
      $obj->title = $data['title'];
      $obj->description = $data['description'];
      $obj->url = $this->urlGenerator->generate($data['route'], $data['params'] ?? []);
      $obj->image = $data['image'];

      array_push($this->boxes, $obj);
      return $this;
   }

   # USERS
   private function listAdmins()
   {
      return $this->create([
         'title' => "Administratorzy systemu",
         'description' => "Tutaj zobaczysz listę administratorow systemu",
         'route' => "app_list_admin",
         'image' => "admin.png"
      ]);
   }

   private function listTeacher()
   {
      return $this->create([
         'title' => "Nauczyciele",
         'description' => "Tutaj zobaczysz listę zatrudnionych nauczycieli",
         'route' => "app_list_teacher",
         'image' => "teacher.png"
      ]);
   }

   private function listStudent()
   {
      return $this->create([
         'title' => "Uczniowie",
         'description' => "Tutaj zobaczysz listę uczniów",
         'route' => "app_list_student",
         'image' => "student.png"
      ]);
   }

   private function addAdmin()
   {
      return $this->create([
         'title' => "Dodaj administratowa systemu",
         'description' => "Tutaj dodasz nowego admiinstratora systemu",
         'route' => "app_register",
         'params' => ["UserType" => UserType::ADMIN],
         'image' => "plus.png"
      ]);
   }

   private function addTeacher()
   {
      return $this->create([
         'title' => "Dodaj nauczyciela",
         'description' => "Tutaj dodasz nowego nauczyciel",
         'route' => "app_register",
         'params' => ["UserType" => UserType::TEACHER],
         'image' => "plus.png"
      ]);
   }

   private function addStudent()
   {
      return $this->create([
         'title' => "Dodaj ucznia",
         'description' => "Tutaj dodasz nowego ucznia",
         'route' => "app_register",
         'params' => ["UserType" => UserType::STUDENT],
         'image' => "plus.png"
      ]);
   }

   # CLASS

   private function listClasses()
   {
      return $this->create([
         'title' => "Lista klas",
         'description' => "Tutaj wyświetlisz wszystkie klasy w szkole",
         'route' => "app_class_list",
         'image' => "class.png"
      ]);
   }

   private function addClass()
   {
      return $this->create([
         'title' => "Dodaj nową klasę",
         'description' => "Tutaj dodasz nową klasę",
         'route' => "app_class_create",
         'image' => "plus.png"
      ]);
   }

   private function myClass()
   {
      return $this->create([
         'title' => "Moja klasa",
         'description' => "Tutaj zobaczysz swoją klasę",
         'route' => "app_myclass_show",
         'image' => "class.png"
      ]);
   }
}