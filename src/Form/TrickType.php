<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
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
              'required' =>false,
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            // if image input field is empty consider a specefic constraint
            'validation_groups' => function(FormInterface $form){
              $cover = $form->get('cover')->getData();
              if ($cover == null){
                return ['Default'];
              }

              return ['Default', 'mandatory'];
            }
        ]);
    }
}
