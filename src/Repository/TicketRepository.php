<?php

namespace App\Repository;

use App\Entity\StatutTicket;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Enum\StatutTicket as EnumStatutTicket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    /**
     * Tous les tickets avec leurs relations principales (évite les N+1)
     */
    public function findAllAvecRelations(): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.statut', 's')
            ->join('t.priorite', 'p')
            ->join('t.categorie', 'c')
            ->join('t.logicielClient', 'lc')
            ->join('lc.client', 'cl')
            ->join('lc.logiciel', 'l')
            ->join('t.createur', 'u')
            ->leftJoin('t.assigne', 'a')
            ->addSelect('s', 'p', 'c', 'lc', 'cl', 'l', 'u', 'a')
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tickets d'un client donné (espace client)
     */
    public function findByClient(int $idClient): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.logicielClient', 'lc')
            ->join('lc.client', 'cl')
            ->join('t.statut', 's')
            ->join('t.priorite', 'p')
            ->join('t.categorie', 'c')
            ->addSelect('s', 'p', 'c', 'lc')
            ->andWhere('cl.id = :idClient')
            ->setParameter('idClient', $idClient)
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tickets d'un client filtrés par statut
     */
    public function findByClientEtStatut(int $idClient, int $idStatut): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.logicielClient', 'lc')
            ->join('lc.client', 'cl')
            ->join('t.statut', 's')
            ->join('t.priorite', 'p')
            ->addSelect('s', 'p', 'lc')
            ->andWhere('cl.id = :idClient')
            ->andWhere('t.statut = :statut')
            ->setParameter('idClient', $idClient)
            ->setParameter('statut', $idStatut)
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
    //**Affiche tous les tickets d'un client, sauf les nouveaux */
    public function findByClientSansNouveau(Utilisateur $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.createur = :user')
            ->andWhere('t.statut != :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', EnumStatutTicket::NOUVEAU->value)
            ->orderBy('t.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
    /**
     * Tickets assignés à un développeur
     */
    public function findByAssigne(Utilisateur $developpeur): array
    {
        return $this->createQueryBuilder('t')
            // ❌ SUPPRIMÉ : ->join('t.priorite', 'p') car ce n'est pas une relation !

            // 🛠️ Changement en leftJoin pour ne pas bloquer les tickets incomplets
            ->leftJoin('t.logicielClient', 'lc')
            ->leftJoin('lc.client', 'cl')
            ->addSelect('lc', 'cl')

            // On filtre sur le développeur passé en paramètre
            ->andWhere('t.assigne = :dev')
            ->setParameter('dev', $developpeur)

            // 🎯 CORRIGÉ : On trie sur la propriété numérique directe 'prioriteCalculee'
            ->orderBy('t.prioriteCalculee', 'DESC')
            ->addOrderBy('t.dateCreation', 'ASC')

            ->getQuery()
            ->getResult();
    }

    /**
     * Tickets non assignés (nouveaux à prendre en charge)
     */
    public function findNonAssignes(): array
    {
        return $this->createQueryBuilder('t')
            // Jointures facultatives pour éviter les erreurs si un champ est vide
            ->leftJoin('t.logicielClient', 'lc')
            ->leftJoin('lc.client', 'cl')
            ->leftJoin('lc.logiciel', 'l')
            ->leftJoin('t.createur', 'u')
            ->addSelect('lc', 'cl', 'l', 'u')

            // 🎯 UNIQUE CONDITION : Le statut doit être "nouveau"
            ->where('t.statut = :statut')
            ->setParameter('statut', \App\Enum\StatutTicket::NOUVEAU)

            // Les tris pour avoir les plus urgents en premier
            ->orderBy('t.prioriteCalculee', 'DESC')
            ->addOrderBy('t.dateCreation', 'ASC')

            ->getQuery()
            ->getResult();
    }

    /**
     * Tickets filtrés par statut (tableau de bord dev)
     */
    public function findByStatut(int $idStatut): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.statut', 's')
            ->join('t.priorite', 'p')
            ->join('t.logicielClient', 'lc')
            ->join('lc.client', 'cl')
            ->leftJoin('t.assigne', 'a')
            ->addSelect('s', 'p', 'lc', 'cl', 'a')
            ->andWhere('t.statut = :statut')
            ->setParameter('statut', $idStatut)
            ->orderBy('p.niveauCriticite', 'ASC')
            ->addOrderBy('t.dateCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compteurs par statut pour les indicateurs du tableau de bord
     * Retourne [['libelle' => 'Nouveau', 'couleur' => '#...', 'total' => 12], ...]
     */
    public function countByStatut(): array
    {
        return $this->createQueryBuilder('t')
            ->select('s.libelle', 's.couleurLib', 'COUNT(t.id) AS total')
            ->join('t.statut', 's')
            ->groupBy('s.id')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Nombre de tickets fermés par jour sur une période donnée (indicateurs)
     */
    public function countFermesParJour(\DateTimeInterface $debut, \DateTimeInterface $fin): array
    {
        return $this->createQueryBuilder('t')
            ->select('DATE(t.dateCloture) AS jour', 'COUNT(t.id) AS total')
            ->andWhere('t.dateCloture BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->groupBy('jour')
            ->orderBy('jour', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Détail complet d'un ticket avec toutes ses relations
     */
    public function findDetailComplet(int $idTicket): ?Ticket
    {
        return $this->createQueryBuilder('t')
            ->join('t.statut', 's')
            ->join('t.priorite', 'p')
            ->join('t.categorie', 'c')
            ->join('t.logicielClient', 'lc')
            ->join('lc.client', 'cl')
            ->join('lc.logiciel', 'l')
            ->join('t.createur', 'u')
            ->leftJoin('t.assigne', 'a')
            ->leftJoin('t.destinataire', 'd')
            ->leftJoin('t.compteRendu', 'cr')
            ->addSelect('s', 'p', 'c', 'lc', 'cl', 'l', 'u', 'a', 'd', 'cr')
            ->andWhere('t.id = :id')
            ->setParameter('id', $idTicket)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
