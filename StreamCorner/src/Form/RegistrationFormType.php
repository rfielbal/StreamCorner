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
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'given-name'],
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Veuillez saisir votre prenom.',
                    ]),
                    new Length([
                        'min'=> 2,
                        'minMessage'=> 'Votre prenom doit contenir au moins {{ limit }} caracteres.',
                        'max'=> 100,
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'family-name'],
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Veuillez saisir votre nom.',
                    ]),
                    new Length([
                        'min'=> 2,
                        'minMessage'=> 'Votre nom doit contenir au moins {{ limit }} caracteres.',
                        'max'=> 100,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email'],
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
                'constraints' => [
                    new NotBlank([
                        'message'=> 'Veuillez saisir un mot de passe.',
                    ]),
                    new Length([
                        'min'=> 6,
                        'minMessage'=> 'Votre mot de passe doit contenir au moins {{ limit }} caracteres.',
                        'max'=> 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
