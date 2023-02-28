<?php

namespace App\Form\Lesson;

use App\Entity\Lesson\Lesson;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonFormType extends AbstractLessonFormType
{
   public function configureOptions(OptionsResolver $resolver): void
   {
      $resolver->setDefaults([
         'data_class' => Lesson::class,
         'lessonTimes' => null
      ]);
   }
}