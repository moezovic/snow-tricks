<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('niveau', ChoiceType::class, ['choices'=>array_flip(Trick::NIVEAU), 'label' => 'Niveau de difficulté'])
            ->add('trick_group' , ChoiceType::class, ['choices'=>array_flip(Trick::TRICK_GROUP), 'label' => 'Type de figure'])
            ->add('cover', FileType::class, [
              'label' => 'Image couverture',
            ])
              ->add('images', FileType::class, [
              'label' => 'Image',
              'multiple' => true
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
