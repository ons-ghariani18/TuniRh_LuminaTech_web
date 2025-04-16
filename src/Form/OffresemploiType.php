<?php

namespace App\Form;

use App\Entity\Offresemploi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OffresemploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'attr' => ['placeholder' => 'Ex. Développeur Web'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre est obligatoire']),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => ['rows' => 5, 'placeholder' => 'Décrivez les responsabilités...'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La description est obligatoire']),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'La description doit contenir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('competences_requises', TextType::class, [
                'label' => 'Compétences Requises',
                'required' => true,
                'attr' => ['placeholder' => 'Ex. PHP, Symfony, JavaScript'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Les compétences requises sont obligatoires']),
                    new Assert\Length([
                        'min' => 5,
                        'minMessage' => 'Les compétences requises doivent contenir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'required' => true,
                'attr' => ['placeholder' => 'Ex. Paris'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le lieu est obligatoire']),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'Le lieu ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('type_contrat', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Freelance' => 'Freelance',
                ],
                'required' => true,
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type de contrat est obligatoire']),
                    new Assert\Choice([
                        'choices' => ['CDI', 'CDD', 'Freelance'],
                        'message' => 'Le type de contrat doit être CDI, CDD ou Freelance',
                    ]),
                ],
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Ouverte' => 'Ouverte',
                    'Fermée' => 'Fermée',
                    'En attente' => 'En attente',
                ],
                'required' => true,
                'data' => 'Ouverte',
                'attr' => ['class' => 'form-select'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le statut est obligatoire']),
                    new Assert\Choice([
                        'choices' => ['Ouverte', 'Fermée', 'En attente'],
                        'message' => 'Le statut doit être Ouverte, Fermée ou En attente',
                    ]),
                ],
            ])
            ->add('date_publication', DateType::class, [
                'label' => 'Date de Publication',
                'widget' => 'single_text',
                'required' => true,
                'data' => new \DateTime(),
                'attr' => ['class' => 'form-control datepicker'],
                'constraints' => [
                    new Assert\NotNull(['message' => 'La date de publication est obligatoire']),
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de publication ne peut pas être dans le futur',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offresemploi::class,
        ]);
    }
}