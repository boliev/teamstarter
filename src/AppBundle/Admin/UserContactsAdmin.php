<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Contact;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserContactsAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('contact', EntityType::class, ['class' => Contact::class, 'required' => true])
            ->add('visible', CheckboxType::class, ['required' => false])
            ->add('prefered', CheckboxType::class, ['required' => false])
            ->add('additional', TextType::class, ['required' => false])
            ->add('number', TextType::class, ['required' => false])

        ;
    }
}
