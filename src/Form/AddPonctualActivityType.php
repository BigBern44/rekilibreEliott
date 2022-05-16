<?php

namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\ActivityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

class AddPonctualActivityType extends ActivityType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('type')
            ->remove('day')
            ->add('fromDateTime', DateType::class, [
                'label' => "Du",
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#add_ponctual_activity_fromDateTime',
                ]
            ])
            ->add('toDateTime', DateType::class, [
                'label' => "au",
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#add_ponctual_activity_toDateTime',
                ]
            ])
            ->add('fromTime', TimeType::class, [
                'label' => "de",
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'timepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#add_ponctual_activity_fromTime',
                ]
            ])
            ->add('toTime', TimeType::class, [
                'label' => "à",
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'timepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#add_ponctual_activity_toTime',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
