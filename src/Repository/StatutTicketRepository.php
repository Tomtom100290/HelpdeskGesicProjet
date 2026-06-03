<?php

namespace App\Repository;

use App\Entity\StatutTicket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StatutTicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutTicket::class);
    }

    public function findActifs(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.topActif = :actif')
            ->setParameter('actif', true)
            ->orderBy('s.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByLibelle(string $libelle): ?StatutTicket
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.libelle = :libelle')
            ->setParameter('libelle', $libelle)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
