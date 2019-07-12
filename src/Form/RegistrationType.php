<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Email: ',
                'required' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'les deux mots de passe doivent correspondre.',
                'first_options' => [
                    'label' => 'Mot de passe: ',
                    'help' => 'minimum 6 lettres et 1 chiffre.'
                ], 'second_options' => [
                    'label' => 'Tapez le mot de passe à nouveau: ',
                    'help' => 'le même mot de passe.'
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Un username: ',
                'required' => true,
                'help' => 'Servira à vous identifiez sur le site.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
