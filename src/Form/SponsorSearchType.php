<?php
// src/Form/SponsorSearchType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SponsorSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du sponsor',
                'required' => false,
                'attr' => ['placeholder' => 'Rechercher par nom']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de sponsor',
                'choices' => [
                    'Financier' => 'Financier',
                    'Matériel' => 'Matériel',
                    'Services' => 'Services',
                    'Médiatique' => 'Médiatique',
                ],
                'required' => false,
                'placeholder' => 'Tous les types',
                'attr' => ['class' => 'form-control']
            ]);
           
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return ''; // Retourne une chaîne vide pour avoir des URLs propres
    }
}