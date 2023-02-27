<?php

namespace App\Form\Lesson;

use App\Entity\Lesson\Lesson;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonFormType extends AbstractLessonFormType
{
   public function buildForm(FormBuilderInterface $builder, array $options): void
   {
      //! ADD HERE LESSON STATUS !\\
      // $builder
      //    ->add('lessonTime', EntityType::class, [
      //       'class' => LessonTime::class,
      //       'placeholder' => "Wybierz godziny",
      //       'required' => true,
      //       'attr' => [
      //          'class' => "form-control"
      //       ],
      //       'choice_label' => "time",
      //       'choices' => $options['lessonTimes']
      //    ]);

      parent::buildForm($builder, $options);
   }


   public function configureOptions(OptionsResolver $resolver): void
   {
      $resolver->setDefaults([
         'data_class' => Lesson::class,
         'lessonTimes' => null
      ]);
   }
}