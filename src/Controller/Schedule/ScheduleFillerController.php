<?php

namespace App\Controller\Schedule;

use App\Entity\Schedule\Schedule;
use App\Entity\Schedule\ScheduleTemplate;
use App\Entity\SchoolClass\SchoolClass;
use App\Form\Schedule\InitializerFormType;
use App\Service\FormErrors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/schedule")
 */
class ScheduleFillerController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $entityManager)
   {
      $this->em = $entityManager;
   }

   /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/fill/{class_id}/{day}", name="app_schedule_fill")
    * @Method("POST")
    */
   public function fill(Request $request, FormErrors $formErrors): Response
   {
      $day = $request->get('day');
      $class = $this->getClass($request->get('class_id'));

      $form = $this->createForm(InitializerFormType::class, null, [
         'action' => $this->generateUrl("app_schedule_fill", ['day' => $day, 'class_id' => $class->getId()])
      ]);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
         $min = $form->get("from")->getData();
         $max = $form->get("to")->getData();

         $dates = [];
         $scheduleTemplateRepository = $this->em->getRepository(ScheduleTemplate::class);
         $template = $scheduleTemplateRepository->findBy(['day' => $day, 'class' => $class]);

         while ($max >= $min) {
            if (ucfirst($day) == $min->format("l")) {
               $dates[] = clone ($min);
            }

            $min->modify("+1 day");
         }

         if (!empty($dates)) {
            $this->em->getRepository(Schedule::class)->removeIn($class, $dates);
         }

         foreach ($dates as $date) {
            foreach ($template as $item) {
               $object = new Schedule();
               $object->setClass($item->getClass());
               $object->setClassTime($item->getClassTime());
               $object->setTeacher($item->getTeacher());
               $object->setSubject($item->getSubject());
               $object->setPlace($item->getPlace());
               $object->setDate($date);

               $this->em->persist($object);
            }
         }

         $this->em->flush();
         $this->addFlash('success', "Harmonogram został utworzony");
      } else {
         $formErrors->set($form);
      }

      return $this->redirectToRoute('app_scheduleTemplate_show', ['day' => $day, 'class_id' => $class->getId()]);
   }

   // ! IN FUTURE ADD THIS METHOD TO SERVIS
   private function getClass($id)
   {
      $class = $this->em->getRepository(SchoolClass::class)->findOneBy(['id' => $id]);

      if (!$class) {
         dump("klasa nie istnieje");
         die();
      }

      return $class;
   }
}