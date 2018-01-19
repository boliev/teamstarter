<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class UserProfileType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UserProfileType constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'user.first_name'])
            ->add('lastName', TextType::class, ['label' => 'user.last_name'])
            ->add('newPassword', PasswordType::class, ['label' => 'user.new_password', 'mapped' => false, 'required' => false])
            ->add('oldPassword', PasswordType::class, ['label' => 'user.old_password', 'mapped' => false, 'required' => false])
            ->add('submit', SubmitType::class, ['label' => 'submit'])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
