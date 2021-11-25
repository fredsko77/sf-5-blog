<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
            ])
            ->add('slug', TextType::class, [
                'label' => 'Permalien',
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'Résumé',
                'required' => false,
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contrenu',
                'required' => false,
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Publié' => 'published',
                    'En écriture' => 'in-writing',
                    'En relecture' => 'in-review',
                    'Archivé' => 'archieved',
                ],
            ])
            ->add('uploadedFile', FileType::class, [
                'label' => 'Image de la catégorie',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Cette image n\'est pas valide !',
                    ]),
                ],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
