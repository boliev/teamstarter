<?php

namespace AppBundle\Form\ProjectCreate;

use AppBundle\Entity\Project;
use AppBundle\Entity\Specialization;
use AppBundle\Repository\SpecializationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OpenVacancyType extends AbstractType
{
    /**
     * @var SpecializationRepository
     */
    private $specializationRepository;

    /**
     * OpenVacancyType constructor.
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
            ->add('name', TextType::class, ['label' => 'project.add_vacancy_name'])
            ->add('specialization', EntityType::class, [
                'label' => 'project.add_vacancy_specialization',
                'class' => Specialization::class,
                'choices' => $this->specializationRepository->getListForSelect(),
                'placeholder' => 'project.add_vacancy_chose_specialization', ])
            ->add('description', TextareaType::class, ['label' => 'project.form_description']);
    }
}
