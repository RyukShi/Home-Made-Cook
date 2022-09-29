<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserModificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'My new username : ',
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'My new password : ',
                'mapped' => false,
                'required' => false,
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'Confirm my new password : ',
                // ignored when reading or writing to the User object
                'mapped' => false,
                'required' => false,
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => false,
                'image_uri' => false,
                'label' => 'Your profile picture : ',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Change my profile',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
