<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Registration;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activities', EntityType::class,[
                'class' => Activity::class,
                'choice_label' => 'displayRegistrationName',
                'label' => 'Je participe aux activités hebdomadaires',
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.type = ?1')
                        ->andWhere('a.fromDateTime >= :fromDate')
                        ->orderBy('a.name', 'ASC')
                        ->setParameter(1, 'hebdo')
                        ->setParameter('fromDate', date("Y").'-08-23');
                },
                'attr' => [
                    'data-size' => '5',
                    'data-live-search' => 'true',
                ]
            ])
            ->add('boardCandidate', CheckboxType::class,[
                'label' => 'Je suis candidat(e) au Conseil d\'Administration de l\'association',
                'required' => false,
            ])
            ->add('agreePhoto', CheckboxType::class,[
                'label' => 'J\'autorise l\'association R\'ekilibre à mettre sur son site Internet des photos où je suis présent(e)',
                'required' => false,
            ])
            ->add('membershipCheck', CheckboxType::class,[
                'label' => 'Un chèque d\'adhésion de 25€',
                'required' => false,
            ])
            ->add('activitiesSingleCheck', CheckboxType::class,[
                'label' => 'Un chèque du montant de l\'activité (200€, 220€, 160€)',
                'required' => false,
            ])
            ->add('activitiesMultiChecks', CheckboxType::class,[
                'label' => '3 chèques suivants l\'activité (70€+70€+60€=200€, 80€+70€+70€=220€,60€+50€+50€=160€)',
                'required' => false,
            ])
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Registration::class,
        ]);
    }
}
