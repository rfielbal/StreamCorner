<?php

namespace App\Form;

use App\Entity\Panier;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class PanierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('totalHtPa', NumberType::class, [
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Panier::class,
        ]);
    }
}
