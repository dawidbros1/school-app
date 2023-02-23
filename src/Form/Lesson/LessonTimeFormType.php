<?php

namespace App\Form\Lesson;

use App\Entity\Lesson\LessonTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonTimeFormType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
      $builder
         ->add('fromTime', TimeType::class, [
            'input' => 'datetime',
            'minutes' => range(0, 55, 5),
            'hours' => range(7, 18),
         ])
         ->add('toTime', TimeType::class, [
            'input' => 'datetime',
            'minutes' => range(0, 55, 5),
            'hours' => range(7, 18),
         ])
      ;
   }

   public function configureOptions(OptionsResolver $resolver): void
   {
      $resolver->setDefaults([
         'data_class' => LessonTime::class,
      ]);
   }
}