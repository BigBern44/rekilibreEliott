<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Year;

use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\ActivityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AddHebdoActivityType extends ActivityType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->remove('type')
            ->add('fromDateTime', DateType::class, [
                'label' => "Période du",
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'datepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#add_hebdo_activity_fromDateTime',
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
                    'data-target' => '#add_hebdo_activity_toDateTime',
                ]
            ])
            ->add('fromTime', TimeType::class, [
                'label' => "de",
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'timepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#add_hebdo_activity_fromTime',
                ]
            ])
            ->add('toTime', TimeType::class, [
                'label' => "à",
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'timepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#add_hebdo_activity_toTime',
                ]

                

                
                ])
                ->add('year_id', HiddenType::class, [

                    'data' => 4
                 
                ]);
            
           

            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
