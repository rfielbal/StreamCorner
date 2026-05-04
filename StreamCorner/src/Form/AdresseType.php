<?php

namespace App\Form;

use App\Entity\Adresse;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AdresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rue', TextType::class, [
                'label' => 'Rue',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la rue.',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('cp', TextType::class, [
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le code postal.',
                    ]),
                    new Length([
                        'max' => 10,
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9A-Za-z -]+$/',
                        'message' => 'Le code postal contient des caractères non autorisés.',
                    ]),
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la ville.',
                    ]),
                    new Length([
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('pays', TextType::class, [
                'label' => 'Pays',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le pays.',
                    ]),
                    new Length([
                        'max' => 100,
                    ]),
                ],
            ])
        ;

        if ($options['include_user']) {
            $builder->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Utilisateur',
                'placeholder' => 'Sélectionnez un utilisateur',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un utilisateur.',
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adresse::class,
            'include_user' => false,
        ]);

        $resolver->setAllowedTypes('include_user', 'bool');
    }
}
