<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', TextareaType::class, [
            'label' => 'dialogs.new_message',
            'required' => true,
            'attr' => [
                'placeholder' => 'dialogs.new_message',
            ],
        ]);

        $builder->add('offer', HiddenType::class, [
            'required' => true,
        ]);
    }
}
