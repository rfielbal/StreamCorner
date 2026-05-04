<?php

namespace App\Form;

use App\Entity\Admin;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emailA', EmailType::class, [
                'label' => 'Email admin',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir l\'email admin.',
                    ]),
                    new Email([
                        'message' => 'Veuillez saisir une adresse email admin valide.',
                    ]),
                    new Length([
                        'max' => 180,
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe admin',
                'mapped' => false,
                'required' => $options['require_password'],
                'attr' => ['autocomplete' => 'new-password'],
                'help' => $options['require_password'] ? null : 'Laissez vide pour conserver le mot de passe admin actuel.',
                'constraints' => $options['require_password'] ? [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe admin.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe admin doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ] : [],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Utilisateur lié',
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
            'data_class' => Admin::class,
            'require_password' => false,
        ]);

        $resolver->setAllowedTypes('require_password', 'bool');
    }
}
