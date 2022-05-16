<?php

namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;
use App\Entity\Location;
use App\Entity\Picture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityRepository;

class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom",
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
                'attr' => [
                    'required' => true,
                ]
            ])
            ->add('fromDateTime', DateType::class, [
                'label' => "Date de début",
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'datepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#activity_fromDateTime',
                ]
            ])
            ->add('toDateTime', DateType::class, [
                'label' => "Heure de fin",
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'datepicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#activity_toDateTime',
                ]
            ])
            ->add('fromTime', TimeType::class, [
                'label' => "Heure de début",
                'widget' => 'single_text',
            ])
            ->add('toTime', TimeType::class, [
                'label' => "Heure de fin",
                'widget' => 'single_text',
            ])
            ->add('maxRegistrations', NumberType::class, [
                'label' => "Nombre d'inscriptions",
            ])
            ->add('type')
            ->add('price', NumberType::class, [
                'label' => "Prix",
            ])
            ->add('users', EntityType::class, [
                'label' => "Participants",
                'class' => User::class,
                'choice_label' => 'displayName',
                'multiple' => true,
                'required' => false,
                'by_reference' => false,
                'attr' => [
                    'data-live-search' => true,
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.firstname','ASC')
                        ->orderBy('u.surname','ASC');
                },
            ])
            ->add('location', EntityType::class, [
                'label' => "Salle",
                'class' => Location::class,
                'choice_label' => 'name',
                'attr' => [
                    'required' => true,
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.name','ASC');
                },
            ])
            ->add('interveners', EntityType::class, [
                'label' => "Intervenants",
                'class' => User::class,
                'choice_label' => 'displayName',
                'multiple' => true,
                'by_reference' => false,
                'attr' => [
                    'data-live-search' => true,
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.intervener = ?1')
                        ->setParameter(1,true)
                        ->orderBy('u.firstname','ASC')
                        ->orderBy('u.surname','ASC');
                },
            ])
            ->add('reiki', CheckboxType::class, [
                'label' => "Reiki",
                'required' => false,
            ])
            ->add('appNotification', CheckboxType::class, [
                'mapped' => false,
                'label' => "Notification application",
                'required' => false,
            ])
            ->add('emailNotification', CheckboxType::class, [
                'mapped' => false,
                'label' => "Notification email",
                'required' => false,
            ])
            ->add('picture', EntityType::class, [
                'label' => "Photo",
                'class' => Picture::class,
                'choice_label' => 'displayName',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'attr' => [
                    'data-live-search' => true,
                ],
            ])
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
