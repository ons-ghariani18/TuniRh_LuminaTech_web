<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersType extends AbstractType
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Entrez le nom',
                    'class' => 'form-control'
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Entrez le prénom',
                    'class' => 'form-control'
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Entrez l\'adresse email',
                    'class' => 'form-control'
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Entrez le mot de passe',
                        'class' => 'form-control'
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez le mot de passe',
                        'class' => 'form-control'
                    ],
                ],
                'mapped' => false, // Ne pas mapper directement à l'entité
            ])
            ->add('tel', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Entrez le numéro de téléphone (ex. +21612345678)',
                    'class' => 'form-control'
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Entrez l\'adresse',
                    'class' => 'form-control'
                ],
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Responsable RH' => 'Responsable RH',
                    'Employé' => 'Employé',
                    'Candidat' => 'candidat',
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
        ;

        // Transformation pour hacher le mot de passe
        $builder->addEventListener(
            \Symfony\Component\Form\FormEvents::SUBMIT,
            function (\Symfony\Component\Form\FormEvent $event) {
                $form = $event->getForm();
                $user = $event->getData();

                if ($plainPassword = $form->get('password')->getData()) {
                    $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}