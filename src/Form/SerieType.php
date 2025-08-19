<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la série',
                'attr' => ['placeholder' => 'Entrez le nom de la série']
            ])
            ->add('overview')
            ->add('status',ChoiceType::class, [
                'choices' => [
                    'En cours' => 'returning',
                    'Terminée' => 'ended',
                    'Annulée' => 'Canceled',
                ],
                'placeholder' => 'Sélectionnez le statut',
                'label' => 'Statut de la série',
                'attr' => ['class' => 'form-select']
            ])
            ->add('vote')
            ->add('popularity')
            ->add('genre')
            ->add('firstAirDate', DateType::class,[
                'widget' => 'single_text',
                'label' => 'Date de première diffusion',
                'html5' => true,
            ])
            ->add('lastAirDate',DateType::class,[
                'widget' => 'single_text',
                'label' => 'Date de dernière diffusion',
                'html5' => true,
                'required' => false, // Optionnel si la série est toujours en cours
            ])
            ->add('tmdbId', TextType::class, [
                'label' => 'ID TMDB',
                'required' => false, // Optionnel si vous ne souhaitez pas l'utiliser
            ])
            ->add('backdrop')
            ->add('poster_file', FileType::class, [
                'label' => 'Image de la série (poster)',
                'required' => false, // Optionnel si vous ne souhaitez pas l'utiliser
                'mapped' => false, // Si vous ne voulez pas lier ce champ à l'entité
                'attr' => ['accept' => 'image/*'], // Pour limiter le type de fichier
                'constraints'=>[
                    new File([
                        'maxSize' => '1024K', // Taille maximale du fichier
                        'maxSizeMessage' => 'Le fichier ne doit pas dépasser 1 Mo',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                    ],
                    'mimeTypesMessage' => 'Format de fichier non valide, veuillez télécharger une image au format JPEG, JPG ou PNG.',
                ])
                ]
            ])
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Enregistrer la série',
                    'attr' => ['class' => 'btn btn-primary']
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
