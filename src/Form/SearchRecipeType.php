<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Thematic;
use App\Data\SearchRecipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SearchRecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Recipe name',
                'required' => false
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Recipe difficulty',
                'required' => false,
                'choices' => [
                    'Easy' => 'Easy',
                    'Medium' => 'Medium',
                    'Hard' => 'Hard',
                ]
            ])
            ->add('recipeCost', ChoiceType::class, [
                'label' => 'Recipe cost',
                'required' => false,
                'choices' => [
                    'Cheap' => 'Cheap',
                    'Medium' => 'Medium',
                    'Expensive' => 'Expensive',
                ]
            ])
            ->add('thematic', EntityType::class, [
                'label' => 'Choose one thematic',
                'required' => false,
                'class' => Thematic::class,
                'choice_label' => 'name'
            ])
            ->add('category', EntityType::class, [
                'label' => 'Choose one category',
                'required' => false,
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Search !',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchRecipe::class,
            'method' => 'GET',
            /* csrf protection disable cause it's just a filter form */
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
