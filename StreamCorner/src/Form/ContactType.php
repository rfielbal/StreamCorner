<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom.',
                    ]),
                    new Length([
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom.',
                    ]),
                    new Length([
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['autocomplete' => 'email'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre email.',
                    ]),
                    new Email([
                        'message' => 'Veuillez saisir une adresse email valide.',
                    ]),
                    new Length([
                        'max' => 180,
                    ]),
                ],
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le sujet.',
                    ]),
                    new Length([
                        'max' => 150,
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['rows' => 7],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre message.',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Votre message doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
