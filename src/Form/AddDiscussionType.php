<?php
namespace App\Form;

use App\Entity\CategorieDiscussion;
use App\Entity\Discussion;
use App\Entity\Registration;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddDiscussionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $categorieAdmise = $options['data']->getCategorieAdmise();
        foreach ($categorieAdmise as $categorie){

        }



        $builder
            ->add('title', TextType::class, [
                'label' => "Titre de votre Discussion",
            ])

            ->add('CategorieDiscussion', EntityType::class,
            [

                    'class' => CategorieDiscussion::class,

                    'multiple' => true,

                    'choices'           => $categorieAdmise,
                    'choice_label'      => 'title',
            ])
            
            ->add('Ajouter', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Discussion::class,

        ]);


    }


}