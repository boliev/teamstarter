<?php

namespace AppBundle\Form\ProjectCreate;

use AppBundle\Entity\Project;
use AppBundle\Entity\ProjectDoc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Url;

class DocType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'project.doc_name'])
            ->add('url', TextType::class, ['label' => 'project.doc_url', 'constraints' => [new Url()]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ProjectDoc::class,
        ));
    }
}
