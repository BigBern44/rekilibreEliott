<?php

namespace App\Form;

use App\Entity\Registration;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationStepOneType extends RegistrationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('postAddress')
            ->remove('lastname')
            ->remove('firstname')
            ->remove('birthdate')
            ->remove('zipCode')
            ->remove('city')
            ->remove('boardCandidate')
            ->remove('agreePhoto')
            ->remove('activities')
            ->remove('plainPassword')
            ->remove('gender')
            ->remove('subscriber')
            ->remove('membershipCheck')
            ->remove('activitiesSingleCheck')
            ->remove('activitiesMultiChecks');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
