<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email' ,TextType::class, [
            "attr" => [ 
                "class" => "form-control",
                "placeholder" => "Entrez votre e-mail",
                ],
                "label" => "Email",
        ])
            ->add('password', PasswordType::class, [
                "attr" => [ 
                    "class" => "form-control",
                    "placeholder" => "Entrez votre mot de passe",
                    ],
                    "label" => "Mot de passe",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
        ));
    }
}