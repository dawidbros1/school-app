<?php

namespace App\Form\Registration;

use App\Interfaces\CustomUserInterface as UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
      $builder
         ->add('firstName', TextType::class, [
            'label' => "Imię",
            'attr' => [
               'placeholder' => "Wprowadź imię",
               'class' => "form-control"
            ],
         ])
         ->add('lastName', TextType::class, [
            'label' => "Nazwisko",
            'attr' => [
               'placeholder' => "Wprowadź nazwisko",
               'class' => "form-control"
            ],
         ])
         ->add('pesel', TextType::class, [
            'label' => "PESEL",
            'attr' => [
               'placeholder' => "Wprowadź numer PESEL",
               'class' => "form-control",
            ],
         ])
      ;
   }

   public function configureOptions(OptionsResolver $resolver): void
   {
      $resolver->setDefaults([
         'data_class' => UserInterface::class,
      ]);
   }
}