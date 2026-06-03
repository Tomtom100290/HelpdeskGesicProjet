<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Messages visibles par le client (exclut les notes internes)
     * Uniquement les messages racines — les réponses sont chargées via la relation
     */
    public function findVisiblesParClient(int $idTicket): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.utilisateur', 'u')
            ->addSelect('u')
            ->leftJoin('m.reponses', 'r')
            ->leftJoin('r.utilisateur', 'ru')
            ->addSelect('r', 'ru')
            ->andWhere('m.ticket = :ticket')
            ->andWhere('m.topActif = :actif')
            ->andWhere('m.messageParent IS NULL')
            ->setParameter('ticket', $idTicket)
            ->setParameter('actif', true)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Tous les messages d'un ticket (développeur — voit aussi les notes internes)
     */
    public function findTousParTicket(int $idTicket): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.utilisateur', 'u')
            ->addSelect('u')
            ->andWhere('m.ticket = :ticket')
            ->andWhere('m.topActif = :actif')
            ->andWhere('m.messageParent IS NULL')
            ->setParameter('ticket', $idTicket)
            ->setParameter('actif', true)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
