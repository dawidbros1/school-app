<?php

namespace App\Service;

use App\Entity\SchoolClass\SchoolClass;
use App\Entity\UserType\Teacher;
use App\Repository\SchoolClass\SchoolClassRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormBuilder
{
   private $methods;

   public function build(&$form)
   {
      foreach ($this->methods as $method) {
         $method($form);
      }

      $this->methods = [];
   }

   public function addButton(string $label)
   {
      $this->methods[] = function (&$form) use ($label) {
         $form->add('submit', SubmitType::class, [
            'label' => $label,
            'attr' => [
               'class' => "btn btn-primary w-100 fw-bold",
            ],
         ]);
      };

      return $this;
   }

   public function addClassList()
   {
      $this->methods[] = function (&$form) {
         $form->add('class', EntityType::class, [
            'label' => "Klasa",
            'class' => SchoolClass::class,
            'placeholder' => "Wybierz klasę [ To pole może być puste ]",
            'required' => false,
            'attr' => [
               'class' => "form-control",
            ],
            'choice_label' => "name",
            'query_builder' => function (SchoolClassRepository $repository) {
               return $repository->getActiveClasses();
            }
         ]);
      };

      return $this;
   }
   public function addClassListWithoutTutor(Teacher $teacher)
   {
      $this->methods[] = function (&$form) use ($teacher) {
         $form->add('class', EntityType::class, [
            'label' => "Klasa",
            'class' => SchoolClass::class,
            'placeholder' => "Wybierz klasę [ To pole może być puste ]",
            'required' => false,
            'attr' => [
               'class' => "form-control",
            ],
            'choice_label' => "name",
            'query_builder' => function (SchoolClassRepository $repository) use ($teacher) {
               return $repository->getActiveClasssesWithoutTutor($teacher);
            }
         ]);
      };

      return $this;
   }
}