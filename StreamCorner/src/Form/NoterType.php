<?php

namespace App\Form;

use App\Entity\Noter;
use App\Entity\Produit;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class NoterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'label' => 'Note',
                'choices' => [
                    '★' => 1,
                    '★★' => 2,
                    '★★★' => 3,
                    '★★★★' => 4,
                    '★★★★★' => 5,
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => ['class' => 'rating-choice-group'],
                'help' => 'Choisissez une note de 1 à 5 étoiles.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une note.',
                    ]),
                    new Range([
                        'min' => 1,
                        'max' => 5,
                        'notInRangeMessage' => 'La note doit être comprise entre {{ min }} et {{ max }} étoiles.',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Avis',
                'attr' => ['rows' => 5],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre avis.',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Votre avis doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
        ;

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
                ->add('user', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'email',
                    'label' => 'Utilisateur',
                    'placeholder' => 'Sélectionnez un utilisateur',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez sélectionner un utilisateur.',
                        ]),
                    ],
                ])
                ->add('produit', EntityType::class, [
                    'class' => Produit::class,
                    'choice_label' => 'designation',
                    'label' => 'Produit',
                    'placeholder' => 'Sélectionnez un produit',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez sélectionner un produit.',
                        ]),
                    ],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Noter::class,
            'admin' => false,
        ]);

        $resolver->setAllowedTypes('admin', 'bool');
    }
}
