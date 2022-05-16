<?php

namespace App\Form;

use App\Entity\Year;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;




class ParametrageDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('annee', EntityType::class, [
            'label' => "Année",
            'class' => Year::class,
            'choice_label' => 'displayname',
            'multiple' => false,
            'required'   => true,
            'preferred_choices' => array(1),
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('y')
                    ->orderBy('y.year', 'ASC');
            }
        ])

        ->add('firstDate', DateType::class, [
            'label' => "Date n°1",
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'datepicker',
                'data-toggle' => 'datetimepicker',
                'data-target' => '#payment_date',
            ]
        ])
        ->add('secondDate', DateType::class, [
            'label' => "Date n°2",
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'datepicker',
                'data-toggle' => 'datetimepicker',
                'data-target' => '#payment_date',
            ]
        ])
        ->add('thirdDate', DateType::class, [
            'label' => "Date n°3",
            'widget' => 'single_text',
            'format' => 'dd/MM/yyyy',
            'attr' => [
                'class' => 'datepicker',
                'data-toggle' => 'datetimepicker',
                'data-target' => '#payment_date',
            ]
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

