<?php

namespace App\Form;

use App\Interfaces\CustomUserInterface as UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
      $builder
         ->add('currentPassword', PasswordType::class, [
            'mapped' => false,
            'label' => "Aktualne hasło",
            'attr' => [
               'placeholder' => "Wprowadź aktualne hasło",
               'class' => "form-control"
            ],
         ])
         ->add('plainPassword', PasswordType::class, [
            'mapped' => false,
            'label' => "Hasło",
            'attr' => [
               'placeholder' => "Wprowadź hasło",
               'class' => "form-control"
            ],
            'constraints' => [
               new NotBlank([
                  'message' => 'Hasło nie może być puste',
               ]),
               new Length([
                  'min' => 6,
                  'minMessage' => 'Twoje hasło powinno zawierać conajmniej {{ limit }} znaków',
                  'max' => 4096,
               ]),
            ],
         ])
         ->add('passwordRepeat', PasswordType::class, [
            'label' => 'Powtórz hasło',
            'mapped' => false,
            'attr' => [
               'placeholder' => "Powtórz hasło",
               'class' => "form-control"
            ],
         ])
         ->add('submit', SubmitType::class, [
            'label' => "Zmień hasło",
            'attr' => [
               'class' => "btn btn-primary w-100 fw-bold",
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