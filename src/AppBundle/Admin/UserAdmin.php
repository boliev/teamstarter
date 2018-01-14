<?php

namespace AppBundle\Admin;

use AppBundle\Entity\UserSkills;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('email', 'text', ['required' => false])
            ->add('firstName', 'text', ['required' => false])
            ->add('lastName', 'text', ['required' => false])
            ->add('enabled', BooleanType::class, ['required' => false])
            ->add('userSkills', EntityType::class, ['class' => UserSkills::class])
            ->add('country', CountryType::class, ['required' => false])
            ->add('city', TextType::class, ['required' => false])
            ->add('likeToDo', TextareaType::class, ['label' => 'user.like_to_do', 'required' => false])
            ->add('expectation', TextareaType::class, ['label' => 'user.expectation', 'required' => false])
            ->add('experience', TextareaType::class, ['label' => 'user.experience', 'required' => false])
            ->add('about', TextareaType::class, ['label' => 'user.about', 'required' => false])
            ->add('aboutFormSkipped', DateTimeType::class, ['label' => 'user.about_form_dkipped_at', 'required' => false])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('email', 'text')
            ->add('firstName')
            ->add('lastName');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('firstName');
    }
}
