<?php

namespace App\Form;

use App\Entity\Registration;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Activity;
use Doctrine\ORM\EntityRepository;

class RegistrationStepThreeType extends RegistrationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        
        

        $dateCloseSeason = $options['data']->getDateCloseSeason();
        
        $builder

            ->add('activities', EntityType::class,[
            'class' => Activity::class,
            'choice_label' => 'displayRegistrationName',
            'label' => 'Je participe aux activités hebdomadaires yop',
            
            'multiple' => true,
            'required' => false,
            'query_builder' => function (EntityRepository $er) use ($dateCloseSeason) {
                return $er->createQueryBuilder('a')      
                    ->where('a.type = ?1')
                    ->andWhere('a.fromDateTime >= :fromDate')
                    ->orderBy('a.name', 'ASC')
                    ->setParameter(1, 'hebdo')
                    ->setParameter('fromDate',  $dateCloseSeason);
                    
            },
            'attr' => [
                'data-size' => '5',
                'data-live-search' => 'true',
            ]
            ])
            ->remove('phone')
            ->remove('emailAddress')
            ->remove('postAddress')
            ->remove('lastname')
            ->remove('firstname')
            ->remove('birthdate')
            ->remove('zipCode')
            ->remove('city')
            ->remove('boardCandidate')
            ->remove('agreePhoto')
            ->remove('plainPassword')
            ->remove('gender')
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
