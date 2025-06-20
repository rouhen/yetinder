<?php

namespace App\Form;

use App\Entity\Yeti;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class YetiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Enter the name of the Yeti',
                ],
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('height', IntegerType::class, [
                'label' => 'Height (cm)',
                'required' => false,
            ])
            ->add('weight', IntegerType::class, [
                'label' => 'Weight (kg)',
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Obrázek',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Yeti::class,
        ]);
    }
}
