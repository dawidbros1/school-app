<?php

namespace App\Form\Schedule;

use DateTime;
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
               'value' => $this->getFromStartValue(),
               'min' => date("Y-m-d"),
               'max' => $this->getMaxDate()
            ]
         ])
         ->add('to', DateType::class, [
            'mapped' => false,
            'widget' => 'single_text',
            'attr' => [
               'value' => $this->getMaxDate(),
               'min' => date("Y-m-d"),
               'max' => $this->getMaxDate()
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

   private function getFromStartValue()
   {
      $date = new DateTime("now");
      $year = $date->format("Y");
      $month = $date->format("m");
      $value = ($month == 7 || $month == 8) ? $date->setDate($year, 9, 1) : $date;

      return $value->format("Y-m-d");
   }

   private function getMaxDate()
   {
      $max = new DateTime("now");
      $year = $max->format("Y");
      $month = $max->format("m");
      $month > 6 ? $year++ : null; // new school year
      $max->setDate($year, 6, 30);

      return $max->format("Y-m-d");
   }
}