<?php

namespace App\Form;

use App\Entity\Registration;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegistrationStepFourType extends RegistrationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('phone')
            ->remove('emailAddress')
            ->remove('postAddress')
            ->remove('lastname')
            ->remove('firstname')
            ->remove('birthdate')
            ->remove('zipCode')
            ->remove('city')
            ->remove('activities')
            ->remove('gender');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
