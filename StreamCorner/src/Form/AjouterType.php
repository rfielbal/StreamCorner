<?php

namespace App\Form;

use App\Entity\Ajouter;
use App\Entity\Panier;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class AjouterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['min' => 1],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la quantité.',
                    ]),
                    new Positive([
                        'message' => 'La quantité doit être supérieure à zéro.',
                    ]),
                ],
            ])
            ->add('prixHt', NumberType::class, [
                'label' => 'Prix HT',
                'input' => 'string',
                'scale' => 2,
                'attr' => ['min' => 0, 'step' => '0.01'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le prix HT.',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le prix HT doit être positif ou égal à zéro.',
                    ]),
                ],
            ])
            ->add('panier', EntityType::class, [
                'class' => Panier::class,
                'choice_label' => 'id',
                'label' => 'Panier',
                'placeholder' => 'Sélectionnez un panier',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un panier.',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ajouter::class,
        ]);
    }
}
