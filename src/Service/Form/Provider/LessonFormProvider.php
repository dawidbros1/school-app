<?php

namespace App\Service\Form\Provider;

use App\Entity\Lesson\Lesson;
use App\Entity\Lesson\LessonStatus;
use App\Entity\Lesson\LessonTime;
use App\Entity\Schedule\Schedule;
use App\Entity\SchoolClass\SchoolClass;
use App\Form\Lesson\LessonFormType;
use App\Service\Form\Provider\AbstractFormProvider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LessonFormProvider extends AbstractFormProvider
{
   public function getCreateFormType(Lesson &$lesson, SchoolClass $class, \DateTime $date, array $data = [])
   {
      $lessonTimes = $this->getAvailableLessonTimes($class, $date, $data, $lesson);

      $form = $this->formFactory->create(LessonFormType::class, $lesson, [
         'label' => "Dodaj lekcje",
         'action' => $this->urlGenerator->generate(
            "app_lesson_create",
            [
               'class_id' => $class->getId(),
               'date' => $date->format("Y-m-d")
            ]
         ),
         'lessonTimes' => $lessonTimes
      ]);

      $this->builder->addButton("Dodaj lekcje")->build($form);

      return $form;
   }

   public function getEditFormType(Lesson &$lesson, array $data = [])
   {
      $lessonTimes = $this->getAvailableLessonTimes($lesson->getClass(), $lesson->getDate(), $data, $lesson);

      $form = $this->formFactory->create(LessonFormType::class, $lesson, [
         'label' => $lesson->getSubject()->getName() . " ( " . $lesson->getLessonTime()->time() . " )",
         'lessonTimes' => $lessonTimes
      ]);

      $form->add('lessonStatus', EntityType::class, [
         'class' => LessonStatus::class,
         'placeholder' => "Wybierz godziny",
         'attr' => [
            'class' => "form-control"
         ],
         'choice_label' => "description",
      ]);

      $this->builder->addSaveButton()->build($form);

      return $form;
   }

   private function getAvailableLessonTimes($class, $date, $data, $lesson)
   {
      $lessonTimes = $data['lessonTimes'] ?? $this->em->getRepository(LessonTime::class)->findAll();
      $schedule = new Schedule($this->em->getRepository(Lesson::class)->getIn($class, [$date]));

      return $this->filter($schedule, $lesson, $lessonTimes);
   }

   private function filter(Schedule $schedule, Lesson $lesson, array $lessonTimes)
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