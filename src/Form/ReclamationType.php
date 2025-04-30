<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'attr' => [
                    'placeholder' => 'Entrez le sujet de la réclamation',
                    'class' => 'form-control'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Décrivez votre réclamation',
                    'class' => 'form-control',
                    'rows' => 4
                ],
            ])
            ->add('dateReclamation', DateTimeType::class, [
                'label' => 'Date de Réclamation',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'data' => new \DateTime(), // Valeur par défaut : date actuelle
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => 'En attente',
                    'En cours' => 'En cours',
                    'Résolu' => 'Résolu',
                    'Rejeté' => 'Rejeté',
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
<<<<<<< HEAD
            ->add('idUser', EntityType::class, [
                'class' => Users::class,
                'choice_label' => function (Users $user) {
                    return $user->getNom() . ' ' . $user->getPrenom();
                },
                'label' => 'Utilisateur',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
=======
            
>>>>>>> origin/gestionUserReclamation
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}