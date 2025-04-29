<?php
// src/Form/EvenementSearchType.php
// src/Form/EvenementSearchType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'required' => false
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
            ->add('sponsor', ChoiceType::class, [
                'label' => 'Sponsor',
                'choices' => $sponsorChoices,
                'required' => false,
                'placeholder' => 'Tous les sponsors'
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
}