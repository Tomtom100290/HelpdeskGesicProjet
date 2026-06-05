<?php

namespace App\Form;

use App\Entity\Logiciel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as TypeIntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogicielType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('description')
            ->add('typeLogiciel')
            ->add('coeffCriticite', TypeIntegerType::class, [

                'empty_data' => '0',  // valeur par défaut si vide
            ])
            ->add('topActif')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Logiciel::class,
        ]);
    }
}
