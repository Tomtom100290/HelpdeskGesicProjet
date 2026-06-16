<?php

namespace App\Form;

use App\Entity\Impact;
use App\Entity\Ticket;
use App\Entity\Urgence;
use App\Entity\Utilisateur;
use App\Entity\LogicielClient;
use App\Repository\LogicielClientRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NvxTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $idClient = $options['id_client'];

        $inputClasses = 'mt-1 block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200/50 focus:outline-none sm:text-sm';
        $labelClasses = 'block text-sm font-semibold text-gray-700 mb-1';

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du ticket',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses, 'placeholder' => 'Ex: Problème d\'accès à la base de données'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses, 'rows' => 8, 'placeholder' => 'Décrivez le problème ici...'],
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
                'label' => 'Quel est l\'objet de votre problème ?',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => 'prompt',
            ])
            ->add('destinataire', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Destinataire (Gésic)',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'placeholder' => '-- Choisir un destinataire Gésic --',
                'required' => false,
                'choice_label' => fn(Utilisateur $u) => $u->getPrenom() . ' ' . $u->getNom(),
                'query_builder' => fn(UtilisateurRepository $ur) => $ur->createFindByEntrepriseQueryBuilder('Gésic'),
            ])
            ->add('logicielClient', EntityType::class, [
                'class' => LogicielClient::class,
                'label' => 'Logiciel concerné',
                'label_attr' => ['class' => $labelClasses],
                'attr' => ['class' => $inputClasses],
                'choice_label' => fn(LogicielClient $lc) => $lc->getLogiciel()->getLibelle() . ' - ' . $lc->getClient()->getRaisonSocial(),
                'query_builder' => fn(LogicielClientRepository $repo) => $repo->findByClient($idClient),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Ticket::class]);
        $resolver->setRequired('id_client');
        $resolver->setAllowedTypes('id_client', 'int');
    }
}
