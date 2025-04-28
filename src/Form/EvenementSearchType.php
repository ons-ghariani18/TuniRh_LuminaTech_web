<?php
// src/Form/EvenementSearchType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\SponsorRepository;

class EvenementSearchType extends AbstractType
{
    private $sponsorRepository;
    
    public function __construct(SponsorRepository $sponsorRepository)
    {
        $this->sponsorRepository = $sponsorRepository;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Récupération des sponsors pour le filtre
        $sponsors = $this->sponsorRepository->findAll();
        $sponsorChoices = [];
        foreach ($sponsors as $sponsor) {
            $sponsorChoices[$sponsor->getNom()] = $sponsor->getIdSp();
        }
        
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'événement',
                'required' => false
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'événement',
                'choices' => [
                    'Conférence' => 'Conférence',
                    'Séminaire' => 'Séminaire',
                    'Formation' => 'Formation',
                    'Exposition' => 'Exposition',
                    'Concert' => 'Concert',
                    'Autre' => 'Autre'
                ],
                'required' => false,
                'placeholder' => 'Tous les types'
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date début (à partir de)',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date fin (jusqu\'à)',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'required' => false
            ])
            ->add('budget_min', NumberType::class, [
                'label' => 'Budget minimum',
                'required' => false
            ])
            ->add('budget_max', NumberType::class, [
                'label' => 'Budget maximum',
                'required' => false
            ])
            ->add('sponsor', ChoiceType::class, [
                'label' => 'Sponsor',
                'choices' => $sponsorChoices,
                'required' => false,
                'placeholder' => 'Tous les sponsors'
            ])
            ->add('rechercher', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'method' => 'GET',
            'csrf_protection' => false, // Désactive la protection CSRF pour permettre le partage des URLs de recherche
        ]);
    }
}