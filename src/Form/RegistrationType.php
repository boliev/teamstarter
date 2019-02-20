<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class, ['label' => 'registration.label_first_name']);
        $builder->add('lastName', TextType::class, ['label' => 'registration.label_last_name']);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getFirstName()
    {
        return 'app_user_registration';
    }

    public function getLastName()
    {
        return 'app_user_registration';
    }
}
