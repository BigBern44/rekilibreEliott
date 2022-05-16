<?php

namespace App\Form;

use App\Entity\Payment;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => "Type",
                'choices' => [
                    'Chèque' => 'Chèque',
                    'Espèce' => 'Espèce',
                    'Virement' => 'Virement',
                ],
                'attr' => [
                    'required' => true,
                ]
            ])
            ->add('value', NumberType::class, [
                'label' => "Montant €",
            ])
            ->add('date', DateType::class, [
                'label' => "Date du paiement",
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'datepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#add_payments_date',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Payment::class,
        ]);
    }
}
