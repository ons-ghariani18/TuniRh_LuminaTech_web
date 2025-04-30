<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
<<<<<<< HEAD
=======
use Symfony\Component\Form\Extension\Core\Type\FileType;
>>>>>>> origin/gestionUserReclamation
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SignUpType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Votre nom',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Votre prénom',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 100,
                        'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'votre@email.com',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est obligatoire']),
                    new Assert\Email(['message' => 'Veuillez fournir une adresse email valide']),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Votre mot de passe',
                        'class' => 'form-control',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez votre mot de passe',
                        'class' => 'form-control',
                    ],
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le mot de passe est obligatoire']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('tel', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Ex. +21612345678',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le téléphone est obligatoire']),
                    new Assert\Regex([
                        'pattern' => '/^\+?[1-9]\d{1,14}$/',
                        'message' => 'Veuillez fournir un numéro de téléphone valide (ex. +21612345678)',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Votre adresse',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse est obligatoire']),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 100,
                        'minMessage' => 'L\'adresse doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'L\'adresse ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
<<<<<<< HEAD
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Responsable RH' => 'Responsable RH',
                    'Employé' => 'Employé',
                    'Candidat' => 'candidat',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le rôle est obligatoire']),
                    new Assert\Choice([
                        'choices' => ['Responsable RH', 'Employé', 'candidat'],
                        'message' => 'Veuillez sélectionner un rôle valide',
=======
            ->add('avatar', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*',
                ],
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2m',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF)',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo',
>>>>>>> origin/gestionUserReclamation
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}