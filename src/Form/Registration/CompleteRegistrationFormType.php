<?php

namespace App\Form\Registration;

use App\Interfaces\CustomUserInterface as UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompleteRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => "Akceptuję regulamin",
                'constraints' => [
                    new IsTrue([
                        'message' => 'Nie zaakceptowano regulaminu',
                    ]),
                ],
                'attr' => [
                    'class' => "form-check-input"
                ],
                'label_attr' => [
                    'class' => "form-check-label"
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Zarejestruj się",
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