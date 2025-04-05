<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Sponsor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType; // Add this import

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('nom')
            ->add('type')
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('lieu')
            ->add('budget')
            ->add('description')
            ->add('google_calendar_event_id')
            ->add('id_Sp', EntityType::class, [
                'class' => Sponsor::class,
                'choice_label' => 'id_Sp',
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
