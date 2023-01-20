<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Adres email",
                'attr' => [
                    'placeholder' => "Wprowadź adres email",
                    'class' => "form-control"
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
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
                'label' => "Hasło"
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Nie zaakceptowano regulaminu',
                    ]),
                ],
                'attr' => [
                    'class' => "form-check-input"
                ],

                'label' => "Akceptuję regulamin",

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
            'data_class' => User::class,
        ]);
    }
}