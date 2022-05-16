<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class ChoixDateListeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('FromDate', DateType::class, [
            'label' => "Date de début",
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
            'attr' => [
                'class' => 'datepicker',
                'data-toggle' => 'datetimepicker',
                'data-target' => '#payment_date',
            ]
        ])
        ->add('ToDate', DateType::class, [
            'label' => "Date de fin",
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'html5' => false,
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

