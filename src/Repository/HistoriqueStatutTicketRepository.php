<?php

namespace App\Repository;

use App\Entity\HistoriqueStatutTicket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HistoriqueStatutTicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueStatutTicket::class);
    }

    /**
     * Historique complet d'un ticket, ordre chronologique
     */
    public function findByTicket(int $idTicket): array
    {
        return $this->createQueryBuilder('h')
            ->join('h.statutAvant', 'sa')
            ->join('h.statutApres', 'sp')
            ->join('h.utilisateur', 'u')
            ->addSelect('sa', 'sp', 'u')
            ->andWhere('h.ticket = :ticket')
            ->setParameter('ticket', $idTicket)
            ->orderBy('h.dateChangement', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Dernier changement de statut d'un ticket
     */
    public function findDernierChangement(int $idTicket): ?HistoriqueStatutTicket
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.ticket = :ticket')
            ->setParameter('ticket', $idTicket)
            ->orderBy('h.dateChangement', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
