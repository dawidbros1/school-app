<?php

namespace App\Form\Lesson;

use App\Entity\Lesson\LessonTemplate;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonTemplateFormType extends AbstractLessonFormType
{
   public function configureOptions(OptionsResolver $resolver): void
   {
      $resolver->setDefaults([
         'data_class' => LessonTemplate::class,
      ]);
   }
}