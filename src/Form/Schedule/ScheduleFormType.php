<?php

namespace App\Form\Schedule;

use App\Entity\Schedule\ClassTime;
use App\Entity\Schedule\Schedule;
use App\Entity\SchoolSubject;
use App\Entity\UserType\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleFormType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
      $builder
         ->add('classTime', EntityType::class, [
            'class' => ClassTime::class,
            'placeholder' => "Wybierz godziny",
            'required' => true,
            'attr' => [
               'class' => "form-control"
            ],
            'choice_label' => "time",
         ])
         ->add('subject', EntityType::class, [
            'class' => SchoolSubject::class,
            'placeholder' => "Wybierz przedmiot",
            'required' => false,
            'attr' => [
               'class' => "form-control"
            ],
            'choice_label' => "name",
         ])
         ->add('teacher', EntityType::class, [
            'class' => Teacher::class,
            'placeholder' => "Wybierz nauczyciela",
            'required' => false,
            'attr' => [
               'class' => "form-control"
            ],
            'choice_label' => "name",
         ])
         ->add('place', TypeTextType::class, [
            'label' => false,
            'attr' => [
               'class' => "form-control",
               'placeholder' => "sala 303 / zdalnie",
            ],
         ])
      ;
   }

   public function configureOptions(OptionsResolver $resolver): void
   {
      $resolver->setDefaults([
         'data_class' => Schedule::class,
      ]);
   }
}