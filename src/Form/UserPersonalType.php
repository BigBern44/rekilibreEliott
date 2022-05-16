<?php

namespace App\Form;

use App\Form\UserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class UserPersonalType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('intervenerActivities')
            ->remove('activities')
            ->remove('roles')
            ->remove('status')
            ->remove('plainPassword')
            ->remove('intervener')
            ->remove('partner')
            ->remove('picture')
            ->remove('description')
            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'dateyearpicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#user_personal_birthdate',
                ]
            ])
            ->add('anonymous', CheckboxType::class, [
                'label' => "Anonyme",
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
