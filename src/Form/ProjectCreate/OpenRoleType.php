<?php

namespace App\Form\ProjectCreate;

use App\Entity\Specialization;
use App\Repository\SpecializationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OpenRoleType extends AbstractType
{
    /**
     * @var SpecializationRepository
     */
    private $specializationRepository;

    /**
     * OpenRoleType constructor.
     *
     * @param SpecializationRepository $specializationRepository
     */
    public function __construct(SpecializationRepository $specializationRepository)
    {
        $this->specializationRepository = $specializationRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'project.add_role_name'])
            ->add('specialization', EntityType::class, [
                'label' => 'project.add_role_specialization',
                'class' => Specialization::class,
                'choices' => $this->specializationRepository->getListForSelect(),
                'placeholder' => 'project.add_role_chose_specialization', ])
            ->add('description', TextareaType::class, ['label' => 'project.form_description'])
            ->add('skills', TextType::class, ['label' => 'project.form_description', 'mapped' => false, 'required' => false]);
    }
}
