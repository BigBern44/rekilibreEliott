<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Activity;
use App\Entity\Picture;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Email",
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'Adhérent' => 'ROLE_ADHERENT',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Gestionnaire' => 'ROLE_GESTION',
                    'Reiki' => 'ROLE_REIKI',
                    'Utilisateur' => 'ROLE_USER',
                ],
                'label' => "Roles",
                'multiple' => true,
                'attr' => [
                    'required' => true,
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => "Mot de passe (Minimum 6 caractères)",
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au minimum {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'required' => false,
            ])
            ->add('gender', ChoiceType::class, [
                'label' => "Genre",
                'choices'  => [
                    'Femme' => 'F',
                    'Homme' => 'H',
                    'Autre' => 'A',
                ],
                'attr' => [
                    'required' => true,
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => "Prénom",
            ])
            ->add('surname', TextType::class, [
                'label' => "Nom",
            ])
            ->add('postAddress', TextType::class, [
                'label' => "Adresse postale",
            ])
            ->add('zipCode', TextType::class, [
                'label' => "Code postal",
            ])
            ->add('city', TextType::class, [
                'label' => "Ville",
            ])
            ->add('birthdate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'dateyearpicker',
                    'data-toggle' => 'datetimepicker',
                    'data-target' => '#user_birthdate',
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => "Téléphone",
            ])
            ->add('status', CheckboxType::class, [
                'label' => "Êtes-vous un adhérent ?",
                'required' => false,
            ])
            ->add('intervener', ChoiceType::class, [
                'label' => "Êtes-vous un intervenant ?",
                'choices'  => [
                    'Non' => false,
                    'Oui' => true,
                ],
            ])
            ->add('partner', TextType::class, [
                'label' => "Nom de l'association ou entreprise (Si intervenant)",
                'required' => false,
            ])
            ->add('activities', EntityType::class, [
                'label' => "Activités",
                'class' => Activity::class,
                'choice_label' => 'displayName',
                'multiple' => true,
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.toDateTime', 'DESC')
                        ->orderBy('a.type' );

                }
            ])
            ->add('intervenerActivities', EntityType::class, [
                'label' => "Activités intervenant",
                'class' => Activity::class,
                'choice_label' => 'displayName',
                'multiple' => true,
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
                'attr' => [
                    'data-live-search' => true,
                ],
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
            ->add('Valider', SubmitType::class)
            ->setAction($options['action']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
