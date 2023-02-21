<?php

namespace App\Form\Schedule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class InitializerFormType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
      $builder
         ->add('from', DateType::class, [
            'mapped' => false,
            'widget' => 'single_text',
            'attr' => [
               'value' => date("Y-m-d"),
               'min' => date("Y-m-d"),
               'max' => date("Y-m-d", strtotime('+1 year'))
            ]
         ])
         ->add('to', DateType::class, [
            'mapped' => false,
            'widget' => 'single_text',
            'attr' => [
               'value' => date("Y-m-d"),
               'min' => date("Y-m-d"),
               'max' => date("Y-m-d", strtotime('+1 year'))
            ]
         ])
         ->add('submit', SubmitType::class, [
            'label' => "Wygeneruj harmonogram",
            'attr' => [
               'class' => "btn btn-primary w-100 fw-bold",
            ],
         ]);
      ;
   }
}