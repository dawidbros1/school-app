<?php

namespace App\Service\Form\Provider;

use App\Service\Form\FormBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractFormProvider
{
   protected $formFactory;
   protected $urlGenerator;
   protected $builder;
   protected $em;

   public function __construct(FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator, FormBuilder $builder, EntityManagerInterface $em)
   {
      $this->formFactory = $formFactory;
      $this->urlGenerator = $urlGenerator;
      $this->builder = $builder;
      $this->em = $em;
   }
}