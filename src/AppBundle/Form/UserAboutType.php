<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAboutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', CountryType::class, ['label' => 'user.country', 'placeholder' => 'user.choose_country'])
            ->add('city', TextType::class, ['label' => 'user.city'])
            ->add('likeToDo', TextareaType::class, ['label' => 'user.like_to_do'])
            ->add('expectation', TextareaType::class, ['label' => 'user.expectation'])
            ->add('experience', TextareaType::class, ['label' => 'user.experience'])
            ->add('about', TextareaType::class, ['label' => 'user.about'])
            ->add('submit', SubmitType::class, ['label' => 'submit'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
