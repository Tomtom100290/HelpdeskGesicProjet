<?php

namespace App\Repository;

use App\Entity\LogicielClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LogicielClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogicielClient::class);
    }

    /**
     * Retourne les logiciels actifs déployés chez un client donné
     */
    public function findByClient(int $idClient)
    {
        return $this->createQueryBuilder('lc')
            ->join('lc.logiciel', 'l')
            ->addSelect('l')
            ->join('lc.client', 'c')
            ->addSelect('c')
            ->andWhere('lc.client = :client')
            ->andWhere('l.topActif = :actif')
            ->setParameter('client', $idClient)
            ->setParameter('actif', true)
            ->orderBy('l.libelle', 'ASC');
    }

    /**
     * Retourne les contrats arrivant à expiration dans N jours
     */
    public function findContratsExpirantBientot(int $jours = 30): array
    {
        $dateLimit = new \DateTimeImmutable("+{$jours} days");

        return $this->createQueryBuilder('lc')
            ->join('lc.client', 'c')
            ->join('lc.logiciel', 'l')
            ->addSelect('c', 'l')
            ->andWhere('lc.dateFinContrat IS NOT NULL')
            ->andWhere('lc.dateFinContrat <= :dateLimit')
            ->andWhere('lc.estActif = :actif')
            ->setParameter('dateLimit', $dateLimit)
            ->setParameter('actif', true)
            ->orderBy('lc.dateFinContrat', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
