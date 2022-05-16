<?php

namespace App\Form;

use App\Entity\Registration;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegistrationStepTwoType extends RegistrationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('phone')
            ->remove('emailAddress')
            ->remove('boardCandidate')
            ->remove('agreePhoto')
            ->remove('activities')
            ->remove('plainPassword')
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
