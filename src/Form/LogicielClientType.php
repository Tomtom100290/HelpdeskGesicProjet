<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Logiciel;
use App\Entity\LogicielClient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LogicielClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateInstallation')
            ->add('versionLogiciel')
            ->add('notes')
            ->add('dateCreation', null, [
                'widget' => 'single_text',
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'raison_social',
            ])
            ->add('logiciel', EntityType::class, [
                'class' => Logiciel::class,
                'choice_label' => 'libelle',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LogicielClient::class,
        ]);
    }
}
