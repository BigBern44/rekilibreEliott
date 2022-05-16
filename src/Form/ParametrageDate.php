<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class ParametrageDate extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstDate', DateType::class, [
            'label' => "FromDate",
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'datepicker',
                'data-toggle' => 'datetimepicker',
                'data-target' => '#payment_date',
            ]
        ])
        ->add('secondDate', DateType::class, [
            'label' => "ToDate",
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'datepicker',
                'data-toggle' => 'datetimepicker',
                'data-target' => '#payment_date',
            ]
        ])
        ->add('thirdDate', DateType::class, [
            'label' => "ToDate",
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'datepicker',
                'data-toggle' => 'datetimepicker',
                'data-target' => '#payment_date',
            ]
        ])
        ->add('fourthDate', DateType::class, [
            'label' => "ToDate",
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'datepicker',
                'data-toggle' => 'datetimepicker',
                'data-target' => '#payment_date',
            ]
        ])
        ->add('Id', HiddenType::class, [
            'label' => "Id",
        ])
        
        ->add('Valider', SubmitType::class)
        ->setAction($options['action'])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            
        ]);
    }
}

