<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];

        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('numTel')
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôle',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Développeur' => 'ROLE_DEVELOPPEUR',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => false,
                'required' => true,
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'raisonSocial',
                'placeholder' => 'Sélectionnez un client',
                'required' => false,
                'label' => 'Entreprise / Client',
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => !$isEdit,
                'label' => $isEdit ? 'Nouveau mot de passe' : 'Mot de passe',
                'help' => $isEdit ? 'Laisser vide pour conserver le mot de passe actuel' : null,
                'constraints' => $isEdit ? [] : [
                    new NotBlank(['message' => 'Veuillez saisir un mot de passe']),
                ],
                'attr' => ['autocomplete' => 'new-password']
            ])
            ->add('imageProfilFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
                'label' => 'Image de profil',
            ])
            ->add('topActif')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'is_edit' => false,
        ]);
    }
}
