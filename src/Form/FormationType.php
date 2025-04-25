<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('title', TextType::class, [
                'empty_data' => '',
            ])
            ->add('description')
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'empty_data' => '',
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'empty_data' => '',
            ])
            ->add('maxParticipants')
            ->add('cost')
        ;


    // Add data transformer to startDate
   $builder->get('startDate')
->addModelTransformer(new class implements DataTransformerInterface {
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            throw new TransformationFailedException('Start date cannot be empty.');
        }
        return $value;
    }
});

// Add data transformer to endDate
//$builder->get('endDate')
//    ->addModelTransformer(new class implements DataTransformerInterface {
//        public function transform($value)
//        {
//            return $value;
//        }
//
//        public function reverseTransform($value)
//        {
//            if (empty($value)) {
//                throw new TransformationFailedException('End date cannot be empty.');
//            }
//            return $value;
//        }
//    });
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
