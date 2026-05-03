<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProjetsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
      

$builder
    ->add('titre', TextType::class, [
        'label' => 'Titre',
        'attr' => ['placeholder' => 'Entrez un titre']
    ])
    ->add('description', TextareaType::class, [
        'label' => 'Description',
        'attr' => [
            'rows' => 5,
            'placeholder' => 'Entrez une description'
        ]
    ])
    ->add('tags', TextType::class)
    ->add('image', FileType::class, [
        'label' => 'Image',
        'mapped' => false,
        'required' => false,
        'constraints' => [
            new File([
                'maxSize' => '5M',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'image/svg+xml',
                ],
                'mimeTypesMessage' => 'Format invalide',
            ])
        ],
    ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
