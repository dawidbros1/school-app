<?php

namespace App\Form\Schedule;

use App\Entity\Schedule\ClassTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassTimeFormType extends AbstractType
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
         'data_class' => ClassTime::class,
      ]);
   }
}