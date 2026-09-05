<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Impact;
use App\Entity\LogicielClient;
use App\Entity\Ticket;
use App\Entity\Urgence;
use App\Entity\Utilisateur;
use App\Enum\StatutTicket;
use App\Repository\LogicielClientRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création et d'édition d'un ticket Helpdesk.
 *
 * Ce formulaire gère la saisie des informations d'un ticket, incluant le typage strict,
 * la liaison avec les entités relatives (Utilisateur, Impact, Urgence, LogicielClient)
 * et le contrôle du statut via l'Enum StatutTicket.
 */
class TicketType extends AbstractType
{
    /**
     * Construit le formulaire de création/modification de ticket.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire Symfony.
     * @param array<string, mixed> $options Les options passées au formulaire (contient 'id_client').
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var int $idClient ID du client requis pour filtrer les logiciels associés */
        $idClient = $options['id_client'];

        // Styles CSS Tailwind réutilisables pour assurer la cohérence visuelle de l'UI
        $inputClasses = 'mt-1 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm transition duration-150 ease-in-out focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 focus:outline-none sm:text-sm';
        $labelClasses = 'block text-sm font-semibold text-gray-700 mb-1';

        $builder
            // Champ : Titre du ticket
            ->add('titre', TextType::class, [
                'label' => 'Titre du ticket',
                'label_attr' => ['class' => $labelClasses],
                'attr' => [
                    'class' => $inputClasses,
                    'placeholder' => 'Ex: Problème d\'accès à la base de données',
                ],
            ])

            // Champ : Description détaillée de la demande
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'label_attr' => ['class' => $labelClasses],
                'attr' => [
                    'class' => $inputClasses,
                    'rows' => 4,
                    'placeholder' => 'Décrivez le problème ici...',
                ],
            ])

            // Champ : Priorité calculée (Lecture seule, gérée dynamiquement par le système)
            ->add('prioriteCalculee', TextType::class, [
                'label' => 'Priorité calculée',
                'disabled' => true,
                'label_attr' => ['class' => $labelClasses],
                'attr' => [
                    'class' => $inputClasses . ' bg-gray-50 cursor-not-allowed font-medium text-gray-500',
                ],
            ])

            // Champ : Créateur du ticket (Relation Entity)
            ->add('createur', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Créateur',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => static fn(Utilisateur $utilisateur): string =>
                $utilisateur->getPrenom() . ' ' . $utilisateur->getNom(),
                'placeholder' => '-- Sélectionner le créateur --',
            ])

            // Champ : Technicien/Intervenant assigné à la résolution
            ->add('assigne', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Intervenant assigné',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => static fn(Utilisateur $utilisateur): string =>
                $utilisateur->getPrenom() . ' ' . $utilisateur->getNom(),
                'placeholder' => '-- Sélectionner un intervenant --',
                'required' => false,
            ])

            // Champ : Destinataire référent Gésic (Filtré par QueryBuilder)
            ->add('destinataire', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Destinataire (Gésic)',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'placeholder' => '-- Choisir un destinataire Gésic --',
                'required' => false,
                'choice_label' => static fn(Utilisateur $utilisateur): string =>
                $utilisateur->getPrenom() . ' ' . $utilisateur->getNom(),
                'query_builder' => static fn(UtilisateurRepository $ur) =>
                $ur->createFindByEntrepriseQueryBuilder('Gésic'),
            ])

            // Champ : Niveau d'impact métier
            ->add('impact', EntityType::class, [
                'class' => Impact::class,
                'label' => 'À quel niveau ce problème vous empêche-t-il de travailler ?',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => 'prompt',
            ])

            // Champ : Degré d'urgence
            ->add('urgence', EntityType::class, [
                'class' => Urgence::class,
                'label' => 'Quel est l’objet de votre problème ?',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => 'prompt',
            ])

            // Champ : Statut du ticket (Interfaçage avec le Backed Enum PHP 8.1+)
            ->add('statut', EnumType::class, [
                'class' => StatutTicket::class,
                'label' => 'Statut du ticket',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
            ])

            // Champ : Logiciel concerné (Filtré dynamiquement selon le client sélectionné)
            ->add('logicielClient', EntityType::class, [
                'class' => LogicielClient::class,
                'label' => 'Logiciel concerné',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => static fn(LogicielClient $lc): string =>
                $lc->getLogiciel()->getLibelle() . ' - ' . $lc->getClient()->getRaisonSocial(),
                'query_builder' => static fn(LogicielClientRepository $repo) =>
                $repo->findByClient($idClient),
            ])

            // Champ : Compte rendu de résolution du ticket
            ->add('compteRendu', TextareaType::class, [
                'label' => 'Compte rendu de résolution',
                'label_attr' => ['class' => $labelClasses],
                'required' => false,
                'attr' => [
                    'class' => $inputClasses,
                    'rows' => 4,
                    'placeholder' => 'Notes de résolution...',
                ],
            ]);
    }

    /**
     * Configure les options de sécurité et d'association de données du formulaire.
     *
     * @param OptionsResolver $resolver Le résolveur d'options Symfony.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'ticket_form',
        ]);

        // Définition et validation du paramètre obligatoire id_client
        $resolver->setRequired('id_client');
        $resolver->setAllowedTypes('id_client', 'int');
    }
}
