<?php

namespace App\Form;

use App\Entity\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class RequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = ["role1","role2","role3"];
        $builder
            // ->add('type')
            // ->add('start_date')
            // ->add('end_date')
            // ->add('half_day')
            // ->add('comment')
            // ->add('status')
            // ->add('user')

            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Télétravail' => 'Télétravail',
                    'Présentiel' => 'Présentiel',
                    'Congés' => 'Congés',
                    'RTT' => 'RTT',
                    'Formation' => 'Formation',
                    'Congés maladies' => 'Congés maladies',
                    'Congés parentaux' => 'Congés parentaux',
                ],
                'placeholder' => 'Sélectionner une option',
                'label' => 'Type de demande',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('start_date', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'date',
                ],
            ])

            ->add('end_date', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(),
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'date',
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'exampleFormControlTextarea1',
                    'rows' => 3,
                ],
                'required' => false,
            ])
            ->add('half_day', ChoiceType::class, [
                'choices' => [
                    'Matin' => 'Matin',
                    'Après-midi' => 'Après-midi',
                ],
                'placeholder' => 'Sélectionner une option',
                'attr' => ['class' => 'form-select'],
                'required' => false,
            ])
;            

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}
