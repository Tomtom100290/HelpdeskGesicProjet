<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findActifs(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.topActif = :actif')
            ->setParameter('actif', true)
            ->orderBy('c.raisonSocial', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAvecLogiciels(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.logicielsClient', 'lc')
            ->leftJoin('lc.logiciel', 'l')
            ->addSelect('lc', 'l')
            ->andWhere('c.topActif = :actif')
            ->setParameter('actif', true)
            ->orderBy('c.raisonSocial', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAvecUtilisateurs(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.utilisateurs', 'u')
            ->addSelect('u')
            ->andWhere('c.topActif = :actif')
            ->setParameter('actif', true)
            ->orderBy('c.raisonSocial', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
