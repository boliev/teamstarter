<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\CountryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAboutType extends AbstractType
{
    private $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profilePicture', FileType::class, ['label' => 'user.profile_picture', 'mapped' => false, 'required' => false])
            ->add('lookingForProject', CheckboxType::class, ['label' => 'user.looking_for_project', 'required' => false])
            ->add('lookingForPartner', CheckboxType::class, ['label' => 'user.looking_for_partner', 'required' => false])
            ->add('country', ChoiceType::class, [
                'mapped' => false,
                'label' => 'user.country',
                'placeholder' => 'user.choose_country',
                'choices' => $this->countryRepository->getLocalizedCountries('ru'),
                'data' => ($options['data']->getCountry() ? $options['data']->getCountry()->getCode() : null),
            ])
            ->add('city', TextType::class, ['label' => 'user.city', 'required' => false])
            ->add('likeToDo', TextareaType::class, ['label' => 'user.like_to_do', 'required' => false])
            ->add('expectation', TextareaType::class, ['label' => 'user.expectation', 'required' => false])
            ->add('experience', TextareaType::class, ['label' => 'user.experience', 'required' => false])
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
