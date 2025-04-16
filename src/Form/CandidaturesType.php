<?php

namespace App\Form;

use App\Entity\Candidatures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

class CandidaturesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_candidat', TextType::class, [
                'label' => 'Votre nom complet',
                'attr' => ['placeholder' => 'Votre nom complet'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est obligatoire']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => ['placeholder' => 'votre@email.com'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est obligatoire']),
                    new Assert\Email(['message' => 'Veuillez fournir une adresse email valide']),
                ],
            ])
            ->add('lettre_motivation', TextareaType::class, [
                'label' => 'Lettre de motivation',
                'attr' => ['rows' => 6, 'placeholder' => 'Expliquez pourquoi vous êtes le candidat idéal...'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La lettre de motivation est obligatoire']),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'La lettre de motivation doit contenir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('cvFile', FileType::class, [
                'label' => 'CV (PDF)',
                'mapped' => false,
                'required' => false, // Temporaire pour tester
                'attr' => ['accept' => 'application/pdf'],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['application/pdf'],
                        'maxSizeMessage' => 'Le fichier ne doit pas dépasser {{ limit }}',
                        'mimeTypesMessage' => 'Veuillez fournir un fichier PDF valide',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidatures::class,
        ]);
    }
}