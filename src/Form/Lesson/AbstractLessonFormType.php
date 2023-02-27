<?php

namespace App\Form\Lesson;

use App\Entity\Lesson\LessonTime;
use App\Entity\SchoolSubject;
use App\Entity\UserType\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractLessonFormType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
      $builder
         ->add('subject', EntityType::class, [
            'class' => SchoolSubject::class,
            'placeholder' => "Wybierz przedmiot",
            'required' => true,
            'attr' => [
               'class' => "form-control"
            ],
            'choice_label' => "name",
         ])
         ->add('teacher', EntityType::class, [
            'class' => Teacher::class,
            'placeholder' => "Wybierz nauczyciela",
            'required' => true,
            'attr' => [
               'class' => "form-control"
            ],
            'choice_label' => "name",
         ])
         ->add('place', TypeTextType::class, [
            'label' => true,
            'attr' => [
               'class' => "form-control",
               'placeholder' => "303 / zdalnie",
            ],
         ])
         ->add('lessonTime', EntityType::class, [
            'class' => LessonTime::class,
            'placeholder' => "Wybierz godziny",
            'required' => true,
            'attr' => [
               'class' => "form-control"
            ],
            'choice_label' => "time",
            'choices' => $options['lessonTimes']
         ]);
      ;
   }
}