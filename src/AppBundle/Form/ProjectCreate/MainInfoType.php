<?php

namespace AppBundle\Form\ProjectCreate;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'project.form_name'])
            ->add('status', EntityType::class, ['label' => 'project.form_status', 'class' => ProjectStatus::class, 'placeholder' => 'project.form_status_placeholder'])
            ->add('country', CountryType::class, ['label' => 'project.form_country', 'placeholder' => 'project.form_country_placeholder', 'required' => false])
            ->add('city', TextType::class, ['label' => 'project.form_city', 'required' => false])
            ->add('mission', TextareaType::class, ['label' => 'project.form_mission'])
            ->add('description', TextareaType::class, ['label' => 'project.form_description']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Project::class,
        ));
    }
}
