<?php

namespace App\Repository;

use App\Entity\Priorite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PrioriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Priorite::class);
    }

    public function findAllOrderedByNiveau(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.niveauCriticite', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByScore(int $score): ?Priorite
    {
        // Critique >= 20, Haute >= 10, Normale >= 5, Faible < 5
        $niveauCriticite = match(true) {
            $score >= 20 => 1,
            $score >= 10 => 2,
            $score >= 5  => 3,
            default      => 4,
        };

        return $this->createQueryBuilder('p')
            ->andWhere('p.niveauCriticite = :niveau')
            ->setParameter('niveau', $niveauCriticite)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
