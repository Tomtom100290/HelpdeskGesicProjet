<?php

namespace App\Form;

use App\Entity\Impact;
use App\Entity\Ticket;
use App\Entity\Urgence;
use App\Enum\StatutTicket;
use App\Entity\Utilisateur;
use App\Entity\LogicielClient;
use App\Repository\UtilisateurRepository;
use Symfony\Component\Form\AbstractType;
use App\Repository\LogicielClientRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $idClient = $options['id_client'];

        // Classes Tailwind communes pour une cohérence parfaite
        $inputClasses = 'mt-1 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm transition duration-150 ease-in-out focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 focus:outline-none sm:text-sm';
        $labelClasses = 'block text-sm font-semibold text-gray-700 mb-1';

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du ticket',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses, 'placeholder' => 'Ex: Problème d\'accès à la base de données']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses, 'rows' => 4, 'placeholder' => 'Décrivez le problème ici...']
            ])
            ->add('prioriteCalculee', TextType::class, [
                'label' => 'Priorité calculée',
                'disabled' => true, // Souvent géré par le système
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses . ' bg-gray-50 cursor-not-allowed font-medium text-gray-500']
            ])
            ->add('createur', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Créateur',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => function (Utilisateur $utilisateur) {
                    return $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                },
                'placeholder' => '-- Sélectionner le créateur --',
            ])
            ->add('assigne', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Intervenant assigné',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => function (Utilisateur $utilisateur) {
                    return $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
                },
                'placeholder' => '-- Sélectionner un intervenant --',
                'required' => false,
            ])
            ->add('destinataire', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Destinataire (Gésic)',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
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
                'label' => 'À quel niveau ce problème vous empêche-t-il de travailler ?',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => 'prompt',
            ])
            ->add('urgence', EntityType::class, [
                'class' => Urgence::class,
                'label' => 'Quel est l’objet de votre problème ?',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => 'prompt',
            ])
            ->add('statut', EnumType::class, [
                'class' => StatutTicket::class,
                'label' => 'Statut du ticket',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
            ])
            ->add('logicielClient', EntityType::class, [
                'class' => LogicielClient::class,
                'label' => 'Logiciel concerné',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => function (LogicielClient $lc) {
                    return $lc->getLogiciel()->getLibelle() . ' - ' . $lc->getClient()->getRaisonSocial();
                },
                'query_builder' => function (LogicielClientRepository $repo) use ($idClient) {
                    return $repo->findByClient($idClient);
                },
            ])
            ->add('compteRendu', TextareaType::class, [
                'label' => 'Compte rendu de résolution',
                'label_attr' => ['class' => $labelClasses],
                'required' => false,
                'attr' => ['class' => $inputClasses, 'rows' => 4, 'placeholder' => 'Notes de résolution...'],
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
