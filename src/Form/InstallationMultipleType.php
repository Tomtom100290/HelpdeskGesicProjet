<?php

namespace App\Form;

use App\DTO\InstallationMultipleDTO;
use App\Entity\Client;
use App\Entity\Logiciel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstallationMultipleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('logiciel', EntityType::class, [
                'class' => Logiciel::class,
                'choice_label' => 'libelle',
                'placeholder' => '-- Choisir le logiciel --',
                'label' => 'Logiciel à déployer'
            ])
            ->add('version', TextType::class, [
                'label' => 'Version (ex: V1.0, V2)',
                'attr' => ['placeholder' => 'V1']
            ])
            ->add('clients', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'raisonSocial',
                'multiple' => true,
                'expanded' => true, // Transforme en cases à cocher (Checkbox)
                'label' => 'Installer chez les clients :'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InstallationMultipleDTO::class,
        ]);
    }
}
