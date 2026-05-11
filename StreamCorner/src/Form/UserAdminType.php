<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir l\'email.',
                    ]),
                    new Email([
                        'message' => 'Veuillez saisir une adresse email valide.',
                    ]),
                    new Length([
                        'max' => 180,
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Droits',
                'choices' => [
                    'Client' => 'ROLE_USER',
                    'Visiteur admin' => 'ROLE_VISITEUR',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'required' => $options['require_password'],
                'attr' => ['autocomplete' => 'new-password'],
                'help' => $options['require_password'] ? null : 'Laissez vide pour conserver le mot de passe actuel.',
                'constraints' => $options['require_password'] ? [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ] : [],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'require_password' => false,
        ]);

        $resolver->setAllowedTypes('require_password', 'bool');
    }
}
