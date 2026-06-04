<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Utilisateur;
use App\Enum\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EnumType;


class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('role', EnumType::class, [
                'class'        => Role::class,
                'choice_label' => fn(Role $role) => $role->label(),
                'label'        => 'Rôle',
            ])


            ->add('nom')
            ->add('prenom')
            ->add('email')
            //->add('entreprise')
            ->add('motDePasse')
            ->add('dateCreation', null, [
                'widget' => 'single_text',
                'mapped' => false,
                'disabled' => true,
            ])
            ->add('topActif')
            ->add('numTel')
            ->add('client', EntityType::class, [
                'class'         => Client::class,
                'choice_label'  => 'raisonSocial',
                'placeholder'   => '-- Sélectionner un client --',
                'label'         => 'Entreprise',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.topActif = :actif')
                        ->setParameter('actif', true)
                        ->orderBy('c.raisonSocial', 'ASC');
                    },
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
