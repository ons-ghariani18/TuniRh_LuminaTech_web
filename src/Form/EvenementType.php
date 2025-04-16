<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Sponsor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'événement',
                'attr' => ['placeholder' => 'Entrez le nom de l\'événement']
            ])
            ->add('type', TextType::class, [
                'label' => 'Type d\'événement',
                'attr' => ['placeholder' => 'Par exemple: Recrutement, Formation, Conférence']
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu',
                'attr' => ['placeholder' => 'Adresse ou lieu virtuel']
            ])
            ->add('budget', MoneyType::class, [
                'label' => 'Budget',
                'currency' => 'EUR',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 5, 'placeholder' => 'Description détaillée de l\'événement']
            ])
           
            ->add('imageFile', FileType::class, [
                'label' => 'Image (JPEG, PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG)',
                    ])
                ],
            ])
            ->add('sponsor', EntityType::class, [
                'class' => Sponsor::class,
                'choice_label' => 'nom', // Supposons que votre entité Sponsor a un champ 'nom'
                'label' => 'Sponsor',
                'required' => false,
                'placeholder' => 'Sélectionnez un sponsor (obligatoire)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}