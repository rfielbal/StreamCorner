<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation', TextType::class, [
                'label' => 'Désignation',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la désignation du produit.',
                    ]),
                    new Length([
                        'max' => 150,
                    ]),
                ],
            ])
            ->add('prixUnitHT', NumberType::class, [
                'label' => 'Prix HT',
                'input' => 'string',
                'scale' => 2,
                'html5' => true,
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
            ->add('image', TextType::class, [
                'label' => 'Image',
                'help' => 'URL complète ou nom de fichier présent dans public/assets/images/products.',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une image.',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 6],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une description.',
                    ]),
                ],
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'attr' => ['min' => 0],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le stock.',
                    ]),
                    new PositiveOrZero([
                        'message' => 'Le stock doit être positif ou égal à zéro.',
                    ]),
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionnez une catégorie',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une catégorie.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
