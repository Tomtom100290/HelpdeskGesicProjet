<?php

namespace App\Repository;

use App\Entity\Logiciel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LogicielRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Logiciel::class);
    }

    public function findActifs(): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.topActif = :actif')
            ->setParameter('actif', true)
            ->orderBy('l.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.typeLogiciel = :type')
            ->andWhere('l.topActif = :actif')
            ->setParameter('type', $type)
            ->setParameter('actif', true)
            ->orderBy('l.libelle', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
