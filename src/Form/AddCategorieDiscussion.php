<?php
namespace App\Form;

use App\Entity\CategorieDiscussion;
use App\Entity\Discussion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCategorieDiscussion extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
            'label' => "Ajouter une catégorie",
        ])

            ->add('roleRequis', ChoiceType::class, [
                'label' => "Qui pourra utiliser cette catégorie ?",
                'choices'  => [
                    'Tout le monde' => 'ROLE_VISITEUR',
                    'Adhérent' => 'ROLE_ADHERENT',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Gestionnaire' => 'ROLE_GESTION',
                    'Reiki' => 'ROLE_REIKI',
                    'Utilisateur' => 'ROLE_USER',
                ],

                'multiple' => true,
            ])

            ->add('Creer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CategorieDiscussion::class,

        ]);


    }


}