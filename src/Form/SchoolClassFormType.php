<?php

namespace App\Form;

use App\Entity\SchoolClass\SchoolClass;
use App\Entity\UserType\Teacher;
use App\Repository\UserType\TeacherRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolClassFormType extends AbstractType
{
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
      $builder
         ->add('name', TextType::class, [
            'label' => "Nazwa klasy",
            'attr' => [
               'placeholder' => "Wprowadź nazwę klasy: [1A 2023/2024]",
               'class' => "form-control"
            ],
         ])
         ->add('teacher', EntityType::class, [
            'label' => "Wychowawca klasy",
            'class' => Teacher::class,
            'placeholder' => "Wybierz wychowawcę klasy [ To pole może być puste ]",
            'required' => false,
            'attr' => [
               'class' => "form-control"
            ],
            'choice_label' => "name",
            'query_builder' => function (TeacherRepository $teacherRepository) {
               return $teacherRepository->createQueryBuilder('t')
                  ->leftJoin('t.class', 'c')
                  ->where('c.status IS NULL');
            },
         ])
         ->add('submit', SubmitType::class, [
            'label' => "Dodaj klasę",
            'attr' => [
               'class' => "btn btn-primary w-100 fw-bold",
            ],
         ])
      ;
   }

   public function configureOptions(OptionsResolver $resolver): void
   {
      $resolver->setDefaults([
         'data_class' => SchoolClass::class,
      ]);
   }
}