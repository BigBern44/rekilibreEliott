<?php

namespace App\Form;

use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Activity;
use App\Entity\Location;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\User;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromTime', TimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
            ])
            ->add('toTime', TimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
            ])
            ->add('day', ChoiceType::class, [
                'label' => "Jour",
                'choices'  => [
                    'Lundi' => '1',
                    'Mardi' => '2',
                    'Mercredi' => '3',
                    'Jeudi' => '4',
                    'Vendredi' => '5',
                    'Samedi' => '6',
                    'Dimanche' => '7',
                ],
            ])
            ->add('activity', EntityType::class, [
                'class' => Activity::class,
                'label' => 'Activité',
                'choice_label' => 'displayName',
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'label' => 'Salle',
                'choice_label' => 'name',
            ])
            ->add('interveners', EntityType::class, [
                'label' => "Intervenants",
                'class' => User::class,
                'choice_label' => 'displayName',
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'data-live-search' => true,
                    'required' => true,
                ]
            ])
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
