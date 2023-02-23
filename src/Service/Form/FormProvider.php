<?php

namespace App\Service\Form;

use App\Entity\Lesson\Lesson;
use App\Entity\Lesson\LessonTemplate;
use App\Entity\SchoolClass\SchoolClass;
use App\Form\Lesson\LessonFormType;
use App\Form\Lesson\LessonTemplateFormType;
use App\Form\Schedule\InitializerFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FormProvider
{
   private $formFactory;
   private $urlGenerator;

   private $builder;

   public function __construct(FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator, FormBuilder $builder)
   {
      $this->formFactory = $formFactory;
      $this->urlGenerator = $urlGenerator;
      $this->builder = $builder;
   }

   public function getInitializerFormType($day, SchoolClass $class)
   {
      $form = $this->formFactory->create(InitializerFormType::class, null, [
         'action' => $this->urlGenerator->generate("app_schedule_fill", ['day' => $day, 'class_id' => $class->getId()])
      ]);

      return $form;
   }

   public function getLessonTemplateFormType(LessonTemplate &$lesson, SchoolClass $class, string $day, string $buttonText)
   {
      $form = $this->formFactory->create(LessonTemplateFormType::class, $lesson, [
         'label' => "Dodaj lekcje",
         'action' => $this->urlGenerator->generate(
            "app_scheduleTemplate_create",
            [
               'class_id' => $class->getId(),
               'day' => $day
            ]
         )
      ]);

      $this->builder->addButton($buttonText)->build($form);

      return $form;
   }

   public function getLessonFormType(Lesson &$lesson, SchoolClass $class, \DateTime $date, string $buttonText)
   {
      $form = $this->formFactory->create(LessonFormType::class, $lesson, [
         'label' => "Dodaj lekcje",
         'action' => $this->urlGenerator->generate(
            "app_schedule_create",
            [
               'class_id' => $class->getId(),
               'date' => $date->format("Y-m-d")
            ]
         )
      ]);

      $this->builder->addButton($buttonText)->build($form);

      return $form;
   }
}