<?php

namespace App\Service;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FormErrors
{
   private $session;
   public function __construct(SessionInterface $session)
   {
      $this->session = $session;
   }

   public function set($form)
   {
      $this->session->remove('errors');

      $errors = [];

      foreach ($form as $child) {
         if (!$child->isValid()) {
            foreach ($child->getErrors() as $error) {
               $errors[$child->getName()][] = $error->getMessage();
            }
         }
      }

      $this->session->set('errors', $errors);
   }

   public function load($form)
   {
      $errors = $this->session->get('errors') ?? [];

      foreach ($errors as $type => $error) {
         foreach ($error as $message) {
            $form->get($type)->addError(new FormError($message));
         }
      }

      $this->session->remove('errors');
   }
}