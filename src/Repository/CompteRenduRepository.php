<?php

namespace App\Repository;

use App\Entity\CompteRendu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CompteRenduRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteRendu::class);
    }

    public function findByTicket(int $idTicket): ?CompteRendu
    {
        return $this->createQueryBuilder('cr')
            ->join('cr.utilisateur', 'u')
            ->addSelect('u')
            ->andWhere('cr.ticket = :ticket')
            ->setParameter('ticket', $idTicket)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Temps moyen de traitement par développeur (indicateurs)
     */
    public function findTempsMoyenParDeveloppeur(): array
    {
        return $this->createQueryBuilder('cr')
            ->select('u.nom', 'u.prenom', 'AVG(cr.tempsTraitementMinutes) AS tempsMoyen', 'COUNT(cr.id) AS nbTickets')
            ->join('cr.utilisateur', 'u')
            ->andWhere('cr.tempsTraitementMinutes IS NOT NULL')
            ->groupBy('u.id')
            ->orderBy('tempsMoyen', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
