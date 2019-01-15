<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\ProjectOpenRole;
use App\Repository\ProjectOpenRoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProposalToProjectType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Project $project */
        $project = $options['project'];
        $builder->add('role', EntityType::class, [
            'class' => ProjectOpenRole::class,
            'choice_label' => 'name',
            'placeholder' => 'project.proposal_choose_role',
            'required' => false,
            'query_builder' => function (ProjectOpenRoleRepository $repository) use ($project) {
                return $repository->getVacantForProjectBuilder($project);
            },
        ]);
        $builder->add('message', TextareaType::class, [
            'label' => 'project.proposal_message',
            'attr' => [
                'placeholder' => 'project.proposal_message',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'project',
        ]);
    }
}
