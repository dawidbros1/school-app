<?php

namespace App\Service\Form\Provider;

use App\Entity\Lesson\LessonTemplate;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\ScheduleTemplate;
use App\Entity\SchoolClass\SchoolClass;
use App\Form\Lesson\LessonTemplateFormType;
use App\Form\Schedule\InitializerFormType;
use App\Service\Form\Provider\AbstractFormProvider;


class LessonTemplateFormProvider extends AbstractFormProvider
{
   public function getCreateFormType(LessonTemplate &$lesson, SchoolClass $class, string $day, array $data = [])
   {
      $lessonTimes = $this->getAvailableLessonTimes($class, $day, $data, $lesson);

      $form = $this->formFactory->create(LessonTemplateFormType::class, $lesson, [
         'label' => "Dodaj lekcje (szablon)",
         'action' => $this->urlGenerator->generate(
            "app_lessonTemplate_create",
            [
               'class_id' => $class->getId(),
               'day' => $day
            ]
         ),
         'lessonTimes' => $lessonTimes
      ]);

      $this->builder->addButton("Dodaj lekcje")->build($form);

      return $form;
   }

   public function getEditFormType(LessonTemplate &$lesson, SchoolClass $class, string $day, array $data = [])
   {
      $lessonTimes = $this->getAvailableLessonTimes($class, $day, $data, $lesson);

      $form = $this->formFactory->create(LessonTemplateFormType::class, $lesson, [
         'label' => $lesson->getSubject()->getName() . " ( " . $lesson->getLessonTime()->time() . " )",
         'lessonTimes' => $lessonTimes
      ]);

      $this->builder->addSaveButton()->build($form);

      return $form;
   }

   private function getAvailableLessonTimes($class, $day, $data, $lesson)
   {
      $lessonTimes = $data['lessonTimes'] ?? $this->em->getRepository(LessonTime::class)->findAll();
      $schedule = new ScheduleTemplate($this->em->getRepository(LessonTemplate::class)->get($class, $day));
      return $this->filter($schedule, $lesson, $lessonTimes);
   }

   private function filter(ScheduleTemplate $schedule, LessonTemplate $lesson, array $lessonTimes)
   {
      foreach ($lessonTimes as $key => $lessonTime) {
         foreach ($schedule->getLessons() as $item) {
            if ($lessonTime == $item->getLessonTime() && $lessonTime != $lesson->getLessonTime()) {
               unset($lessonTimes[$key]);
               break;
            }
         }
      }

      return $lessonTimes;
   }
}