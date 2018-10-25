<?php

namespace AppBundle\Form;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectOpenRole;
use AppBundle\Repository\ProjectOpenRoleRepository;
use AppBundle\Repository\ProjectRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class OfferToSpecialistType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ProjectOpenRoleRepository
     */
    private $openRoleRepository;

    public function __construct(TranslatorInterface $translator, ProjectOpenRoleRepository $openRoleRepository)
    {
        $this->translator = $translator;
        $this->openRoleRepository = $openRoleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Project $project */
        $user = $options['user'];
        $builder->add('project', EntityType::class, [
            'class' => Project::class,
            'choice_label' => 'name',
            'placeholder' => 'project.offer_choose_project',
            'required' => true,
            'query_builder' => function (ProjectRepository $repository) use ($user) {
                return $repository->getPublishedForUserQuery($user);
            },
        ]);

        $formModifier = function (FormInterface $form, Project $project = null) {
            $roles = null === $project ? [] : $this->openRoleRepository->getVacantForProjectBuilder($project)->getQuery()->getResult();

            $form->add('role', EntityType::class, array(
                'class' => ProjectOpenRole::class,
                'placeholder' => 'project.offer_choose_role',
                'choices' => $roles,
                'required' => false,
            ));
        };

        $builder->add('message', TextareaType::class, [
            'label' => 'project.offer_message',
            'attr' => [
                'placeholder' => 'project.offer_message',
            ],
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();

                $formModifier($event->getForm(), $data ? $data->get('project')->getData() : null);
            }
        );

        $builder->get('project')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $project = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $project);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'user',
        ]);
    }
}
