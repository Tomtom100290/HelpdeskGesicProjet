<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Enum\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('email', EmailType::class, ['label' => 'Adresse Email'])
            ->add('role', EnumType::class, [
                'class' => Role::class,
                'label' => 'Rôle de l\'utilisateur'
            ])
            ->add('motDePasse', PasswordType::class, [
                'label' => 'Mot de passe',
                // On mappe ce champ à la propriété $motDePasse de ton entité
                'property_path' => 'motDePasse',
            ])
            ->add('numTel', TextType::class, [
                'label' => 'Téléphone',
                'required' => false
            ])
            # On laisse de côté client, dateCreation, tickets, etc.
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
