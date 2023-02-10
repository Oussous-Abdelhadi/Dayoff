<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            "attr" => [ 
                "class" => "form-control",
                "placeholder" => "Entrez votre nom",
            ],
            "label" => "NOM",
            "constraints" => [
                new NotBlank(),
                new Length([
                    "min" => 2,
                    "minMessage" => "Le nom doit contenir au moins {{ limit }} caractères"
                ]),
                new Regex([
                    "pattern" => "/^[a-zA-Z]+$/",
                    "message" => "Le nom ne peut contenir que des lettres"
                ]),
            ],
        ])
        ->add('firstname', TextType::class, [
            "attr" => [ 
                "class" => "form-control",
                "placeholder" => "Entrez votre prénom",
            ],
            "label" => "Prénom",
            "constraints" => [
                new NotBlank(),
                new Length([
                    "min" => 2,
                    "minMessage" => "Le prénom doit contenir au moins {{ limit }} caractères"
                ]),
                new Regex([
                    "pattern" => "/^[a-zA-Z]+$/",
                    "message" => "Le prénom ne peut contenir que des lettres"
                ]),
            ],
        ])        
        ->add('email' ,TextType::class, [
            "attr" => [ 
                "class" => "form-control",
                "placeholder" => "Entrez votre e-mail",
                ],
                "label" => "Email",
                "constraints" => [
                    new NotBlank(),
                    new Email([
                        "message" => "L'adresse email n'est pas valide"
                    ]),
                ],
        ])
    
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    "class" => "form-control",
                    "placeholder" => "············",
            ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrez votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
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
