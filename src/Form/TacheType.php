<?php

namespace App\Form;

use App\Entity\Tache;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Enum\StatutTache;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('libelle')
            ->add('description')
            ->add('dateRealisation')
            ->add('statut', EnumType::class, [
                'class' => StatutTache::class,
            ])
            ->add('ticket', EntityType::class, [
                'class' => Ticket::class,
                'choice_label' => 'id',
            ])
            ->add('utilisateurAssigne', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
            ])
            ->add('utilisateurCreateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
