<?php

namespace App\Form;

use App\Entity\Registration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\Activity;
use App\Entity\Year;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add('lastname', TextType::class,[
                'label' => 'Nom'
            ])
            ->add('firstname', TextType::class,[
                'label' => 'Prénom'
            ])
            ->add('birthdate', DateType::class,[
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'dateyearpicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#registration_step_two_birthdate',
                ]
            ])
            ->add('emailAddress', TextType::class,[
                'label' => 'Email'
            ])
            ->add('postAddress', TextType::class,[
                'label' => 'Adresse'
            ])
            ->add('zipCode', TextType::class,[
                'label' => 'Code Postal'
            ])
            ->add('city', TextType::class,[
                'label' => 'Ville'
            ])
            ->add('phone', TextType::class,[
                'label' => 'Téléphone'
            ])
            ->add('boardCandidate', CheckboxType::class,[
                'label' => 'Je suis candidat(e) au Conseil d\'Administration de l\'association',
                'required' => false,
            ])
            ->add('agreePhoto', CheckboxType::class,[
                'label' => 'J\'autorise l\'association R\'ekilibre à mettre sur son site Internet des photos où je suis présent(e)',
                'required' => false,
            ])
            ->add('subscriber', CheckboxType::class,[
                'label' => 'Ancien adhérent ' . ((new \DateTime())->format('Y')-1) . '/' . (new \DateTime())->format('Y'),
                'required' => false,
            ])
           
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => "Mot de passe(Minimum 6 caractères)",
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au minimum {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
                'required' => true,
            ])
            ->add('gender', ChoiceType::class, [
                'label' => "Genre",
                'choices'  => [
                    'Homme' => 'H',
                    'Femme' => 'F',
                    'Autre' => 'A',
                ],
                'attr' => [
                    'required' => 'true',
                ]
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           
            'data_class' => Registration::class,

        ]);

        

    }
}
