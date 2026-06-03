<?php

namespace App\Repository;

use App\Entity\Tache;
use App\Entity\Utilisateur;
use App\Enum\StatutTache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    /**
     * Tâches d'un développeur filtrées par statut
     */
    public function findByAssigneEtStatut(Utilisateur $utilisateur, ?StatutTache $statut = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.ticket', 'tk')
            ->addSelect('tk')
            ->andWhere('t.utilisateurAssigne = :user')
            ->setParameter('user', $utilisateur)
            ->orderBy('t.dateCreation', 'ASC');

        if ($statut !== null) {
            $qb->andWhere('t.statut = :statut')
               ->setParameter('statut', $statut);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Tâches du jour d'un développeur (sans date d'échéance ou échéance = aujourd'hui)
     */
    public function findTachesduJour(Utilisateur $utilisateur): array
    {
        $aujourd_hui = new \DateTimeImmutable('today');
        $demain      = new \DateTimeImmutable('tomorrow');

        return $this->createQueryBuilder('t')
            ->leftJoin('t.ticket', 'tk')
            ->addSelect('tk')
            ->andWhere('t.utilisateurAssigne = :user')
            ->andWhere('t.statut != :realisee')
            ->andWhere('t.dateEcheance IS NULL OR t.dateEcheance BETWEEN :debut AND :fin')
            ->setParameter('user', $utilisateur)
            ->setParameter('realisee', StatutTache::REALISEE)
            ->setParameter('debut', $aujourd_hui)
            ->setParameter('fin', $demain)
            ->orderBy('t.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tâches de la semaine d'un développeur
     */
    public function findTachesDeLaSemaine(Utilisateur $utilisateur): array
    {
        $debutSemaine = new \DateTimeImmutable('monday this week');
        $finSemaine   = new \DateTimeImmutable('sunday this week 23:59:59');

        return $this->createQueryBuilder('t')
            ->leftJoin('t.ticket', 'tk')
            ->addSelect('tk')
            ->andWhere('t.utilisateurAssigne = :user')
            ->andWhere('t.statut != :realisee')
            ->andWhere('t.dateEcheance BETWEEN :debut AND :fin')
            ->setParameter('user', $utilisateur)
            ->setParameter('realisee', StatutTache::REALISEE)
            ->setParameter('debut', $debutSemaine)
            ->setParameter('fin', $finSemaine)
            ->orderBy('t.dateEcheance', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tâches liées à un ticket
     */
    public function findByTicket(int $idTicket): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.utilisateurAssigne', 'u')
            ->addSelect('u')
            ->andWhere('t.ticket = :ticket')
            ->setParameter('ticket', $idTicket)
            ->orderBy('t.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
