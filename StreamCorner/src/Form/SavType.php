<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Sav;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SavType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('message', TextareaType::class, [
            'label' => 'Message',
            'attr' => ['rows' => 5],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir le message.',
                ]),
                new Length([
                    'min' => 5,
                    'minMessage' => 'Le message doit contenir au moins {{ limit }} caractères.',
                ]),
            ],
        ]);

        if ($options['admin']) {
            $builder
                ->add('dateMessage', DateTimeType::class, [
                    'label' => 'Date du message',
                    'widget' => 'single_text',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir la date du message.',
                        ]),
                    ],
                ])
                ->add('traitement', ChoiceType::class, [
                    'label' => 'Traitement',
                    'choices' => [
                        'Nouveau' => 'Nouveau',
                        'En cours' => 'En cours',
                        'Traité' => 'Traité',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez sélectionner un état de traitement.',
                        ]),
                    ],
                ])
                ->add('commande', EntityType::class, [
                    'class' => Commande::class,
                    'choice_label' => 'id',
                    'label' => 'Commande',
                    'placeholder' => 'Sélectionnez une commande',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez sélectionner une commande.',
                        ]),
                    ],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sav::class,
            'admin' => false,
        ]);

        $resolver->setAllowedTypes('admin', 'bool');
    }
}
