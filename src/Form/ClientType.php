<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raisonSocial')
            ->add('adresse')
            ->add('ville')
            ->add('codePostal')
            ->add('numTel')
            ->add('email')
            ->add('positionX')
            ->add('positionY')
            ->add('dateCreation', null, [
                'widget' => 'single_text',
                'mapped' => false,
                'disabled' => true,
            ])
            ->add('topActif')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
