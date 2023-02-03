<?php

namespace App\Service;

use App\Interfaces\CustomUserInterface as UserInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FormPasswordManager
{
   private $userPasswordHasher;

   public function __construct(UserPasswordHasherInterface $userPasswordHasher)
   {
      $this->userPasswordHasher = $userPasswordHasher;
   }

   public function passwordsAreIdentical($form)
   {
      if ($form->get('plainPassword')->getData() !== $form->get('passwordRepeat')->getData()) {
         $form->get('passwordRepeat')->addError(new FormError('Hasła nie są takie same'));
         return false;
      }

      return true;
   }

   public function currentPasswordIsCorrect(UserInterface $user, $form)
   {
      $currentPassword = $form->get("currentPassword")->getData();

      if (!$this->userPasswordHasher->isPasswordValid($user, $currentPassword)) {
         $form->get('currentPassword')->addError(new FormError('Aktualne hasło jest nieprawidłowe'));
         return false;
      }

      return true;
   }

   public function getHashedPassword(UserInterface $user, $form)
   {
      return $this->userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData());
   }
}