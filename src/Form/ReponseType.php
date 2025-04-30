<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\Reponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['rows' => 5, 'class' => 'form-control'],
            ])
            ->add('idReclamRep', EntityType::class, [
                'class' => Reclamation::class,
                'choice_label' => 'sujet',
                'label' => 'Réclamation associée',
                'attr' => ['class' => 'form-control'],
                'disabled' => $options['reclamation_locked'],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
            'reclamation_locked' => false,
        ]);
    }
}
