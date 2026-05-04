<?php

namespace App\Form;

use App\Entity\Adresse;
use App\Entity\Commande;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['admin']) {
            $builder
                ->add('dateCommande', DateTimeType::class, [
                    'label' => 'Date de commande',
                    'widget' => 'single_text',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir la date de commande.',
                        ]),
                    ],
                ])
                ->add('totalHtCo', NumberType::class, [
                    'label' => 'Total HT',
                    'input' => 'string',
                    'scale' => 2,
                    'attr' => ['min' => 0, 'step' => '0.01'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir le total HT.',
                        ]),
                        new PositiveOrZero([
                            'message' => 'Le total HT doit être positif ou égal à zéro.',
                        ]),
                    ],
                ])
                ->add('totalTaxe', NumberType::class, [
                    'label' => 'TVA',
                    'input' => 'string',
                    'scale' => 2,
                    'attr' => ['min' => 0, 'step' => '0.01'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir la TVA.',
                        ]),
                        new PositiveOrZero([
                            'message' => 'La TVA doit être positive ou égale à zéro.',
                        ]),
                    ],
                ])
                ->add('total', NumberType::class, [
                    'label' => 'Total TTC',
                    'input' => 'string',
                    'scale' => 2,
                    'attr' => ['min' => 0, 'step' => '0.01'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir le total TTC.',
                        ]),
                        new PositiveOrZero([
                            'message' => 'Le total TTC doit être positif ou égal à zéro.',
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
                ->add('adresse', EntityType::class, [
                    'class' => Adresse::class,
                    'choice_label' => fn (Adresse $adresse): string => sprintf('%s, %s %s', $adresse->getRue(), $adresse->getCp(), $adresse->getVille()),
                    'label' => 'Adresse',
                    'placeholder' => 'Sélectionnez une adresse',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez sélectionner une adresse.',
                        ]),
                    ],
                ])
            ;

            return;
        }

        $builder
            ->add('adresse', EntityType::class, [
                'class' => Adresse::class,
                'choices' => $options['adresses'],
                'choice_label' => fn (Adresse $adresse): string => sprintf('%s, %s %s', $adresse->getRue(), $adresse->getCp(), $adresse->getVille()),
                'label' => 'Adresse de livraison',
                'placeholder' => 'Sélectionnez une adresse',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une adresse de livraison.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'admin' => false,
            'adresses' => [],
        ]);

        $resolver->setAllowedTypes('admin', 'bool');
        $resolver->setAllowedTypes('adresses', 'array');
    }
}
