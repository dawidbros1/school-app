<?php

namespace App\Controller;

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
   }

   private function admin()
   {
      $this->listTeacher()->listStudent();
      $this->addTeacher()->addStudent();
   }

   private function teacher()
   {
      //  Może wyświetlać własną klasę
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

   private function listAdmins()
   {
      return $this->create([
         'title' => "Administratorzy systemu",
         'description' => "Tutaj zobaczysz listę administratorow systemu",
         'route' => "list_admin",
         'image' => "admin.png"
      ]);
   }

   private function listTeacher()
   {
      return $this->create([
         'title' => "Nauczyciele",
         'description' => "Tutaj zobaczysz listę zatrudnionych nauczycieli",
         'route' => "list_teacher",
         'image' => "teacher.png"
      ]);
   }

   private function listStudent()
   {
      return $this->create([
         'title' => "Uczniowie",
         'description' => "Tutaj zobaczysz listę uczniów",
         'route' => "list_student",
         'image' => "student.png"
      ]);
   }

   private function addAdmin()
   {
      return $this->create([
         'title' => "Dodaj administratowa systemu",
         'description' => "Tutaj dodasz nowego admiinstratora systemu",
         'route' => "app_register_init",
         'params' => ["type" => "admin"],
         'image' => "plus.png"
      ]);
   }

   private function addTeacher()
   {
      return $this->create([
         'title' => "Dodaj nauczyciela",
         'description' => "Tutaj dodasz nowego nauczyciel",
         'route' => "app_register_init",
         'params' => ["type" => "teacher"],
         'image' => "plus.png"
      ]);
   }

   private function addStudent()
   {
      return $this->create([
         'title' => "Dodaj ucznia",
         'description' => "Tutaj dodasz nowego ucznia",
         'route' => "app_register_init",
         'params' => ["type" => "student"],
         'image' => "plus.png"
      ]);
   }
}