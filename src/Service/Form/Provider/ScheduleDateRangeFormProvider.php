<?php

namespace App\Service\Form\Provider;

use App\Entity\SchoolClass\SchoolClass;
use App\Form\Schedule\InitializerFormType;
use App\Service\Form\Provider\AbstractFormProvider;


class ScheduleDateRangeFormProvider extends AbstractFormProvider
{
   public function getFormType($day, SchoolClass $class)
   {
      $form = $this->formFactory->create(InitializerFormType::class, null, [
         'action' => $this->urlGenerator->generate("app_schedule_fill", ['day' => $day, 'class_id' => $class->getId()])
      ]);

      return $form;
   }

}