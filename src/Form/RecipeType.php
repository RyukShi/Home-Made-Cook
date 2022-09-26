<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Category;
use App\Entity\Thematic;
use App\Entity\Tag;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Recipe name',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Recipe description',
                ],
            ])
            ->add('ingredients', CollectionType::class, [
                'label' => false,
                'entry_type' => IngredientType::class,
                'entry_options' => ['label' => false],
                // Allow to add new ingredient
                'allow_add' => true,
                // Allow to delete ingredient
                'allow_delete' => true,
            ])
            ->add('tags', EntityType::class, [
                'label' => 'Select tags : ',
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('thematic', EntityType::class, [
                'label' => 'Choose one thematic : ',
                'class' => Thematic::class,
                'choice_label' => 'name',
                'multiple' => false
            ])
            ->add('category', EntityType::class, [
                'label' => 'Choose one category : ',
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('difficulty', ChoiceType::class, [
                'label' => 'Recipe difficulty : ',
                'choices' => [
                    'Easy' => 'Easy',
                    'Medium' => 'Medium',
                    'Hard' => 'Hard',
                ]
            ])
            ->add('preparationTime', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Recipe preparation time (minutes)',
                    'min' => 5,
                    'step' => 5,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
