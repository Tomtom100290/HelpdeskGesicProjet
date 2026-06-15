<?php

namespace App\Form;

use App\Entity\CategorieTicket;
use App\Entity\CompteRendu;
use App\Entity\Impact;
use App\Entity\LogicielClient;
use App\Enum\StatutTicket;
use App\Entity\Ticket;
use App\Entity\Urgence;
use App\Entity\Utilisateur;
use App\Repository\LogicielClientRepository;
use App\Repository\UtilisateurRepository; // Ajout du use pour le repository
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $idClient = $options['id_client']; // Récupération de l'id_client passé en option
        $builder
            ->add('titre')
            ->add('description')
            ->add('prioriteCalculee')



            // Affichage Prénom + Nom plutôt que l'ID pour le créateur
            ->add('createur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => function (Utilisateur $utilisateur) {
                    return $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                },
                'placeholder' => '-- Sélectionner le créateur --',
            ])

            // Affichage Prénom + Nom plutôt que l'ID pour l'assigné
            ->add('assigne', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => function (Utilisateur $utilisateur) {
                    return $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                },
                'placeholder' => '-- Sélectionner un intervenant --',
                'required' => false,
            ])

            // ✅ Modification : Filtre pour n'avoir que les utilisateurs de chez Gésic
            ->add('destinataire', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Destinataire (Gésic)',
                'placeholder' => '-- Choisir un destinataire Gésic --',
                'required' => false,
                'choice_label' => function (Utilisateur $utilisateur) {
                    return $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                },
                'query_builder' => function (UtilisateurRepository $ur) {
                    return $ur->createFindByEntrepriseQueryBuilder('Gésic');
                },
            ])

            ->add('impact', EntityType::class, [
                'class' => Impact::class,
                'choice_label' => 'prompt',
                'label' => '"À quel niveau ce problème vous empêche-t-il de travailler ?"',
            ])
            ->add('urgence', EntityType::class, [
                'class' => Urgence::class,
                'choice_label' => 'prompt',
                'label' => '"Quelle est l\'objet de votre problème ?"',
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutTicket::class,
            ])
            ->add('logicielClient', EntityType::class, [
                'class' => LogicielClient::class,
                'choice_label' => function (LogicielClient $lc) {
                    return $lc->getLogiciel()->getLibelle() . ' - ' . $lc->getClient()->getRaisonSocial();
                },
                'query_builder' => function (LogicielClientRepository $repo) use ($idClient) {
                    return $repo->findByClient($idClient);
                },
            ])

            ->add('compteRendu', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 5],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);

        $resolver->setRequired('id_client');
        $resolver->setAllowedTypes('id_client', 'int');
    }
}
