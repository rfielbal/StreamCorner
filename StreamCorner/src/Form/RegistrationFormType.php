<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'attr' => ['autocomplete' => 'given-name'],
                'invalid_message' => 'Veuillez saisir un prénom valide.',
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Veuillez saisir votre prénom.',
                    ]),
                    new Length([
                        'min'=> 2,
                        'minMessage'=> 'Votre prénom doit contenir au moins {{ limit }} caractères.',
                        'max'=> 100,
                        'maxMessage'=> 'Votre prénom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'attr' => ['autocomplete' => 'family-name'],
                'invalid_message' => 'Veuillez saisir un nom valide.',
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Veuillez saisir votre nom.',
                    ]),
                    new Length([
                        'min'=> 2,
                        'minMessage'=> 'Votre nom doit contenir au moins {{ limit }} caractères.',
                        'max'=> 100,
                        'maxMessage'=> 'Votre nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email'],
                'invalid_message' => 'Veuillez saisir une adresse email valide.',
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Veuillez saisir votre email.',
                    ]),
                    new Email([
                        'message'=> 'Veuillez saisir une adresse email valide.',
                    ]),
                    new Length([
                        'max'=> 180,
                        'maxMessage'=> 'Votre email ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'data' => false,
                'constraints' => [
                    new IsTrue([
                        'message'=> 'Veuillez accepter les conditions d\'utilisation.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'invalid_message' => 'Veuillez saisir un mot de passe valide.',
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Veuillez saisir un mot de passe.',
                    ]),
                    new Length([
                        'min'=> 6,
                        'minMessage'=> 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max'=> 4096,
                        'maxMessage'=> 'Votre mot de passe ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_message' => 'Le formulaire d’inscription a expiré. Veuillez réessayer.',
        ]);
    }
}
