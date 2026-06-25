<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use App\Enum\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    public function findActifs(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.topActif = :actif')
            ->setParameter('actif', true)
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByRole(Role $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role = :role')
            ->andWhere('u.topActif = :actif')
            ->setParameter('role', $role)
            ->setParameter('actif', true)
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findDeveloppeurs(): array
    {
        return $this->findByRole(Role::DEVELOPPEUR);
    }
    /*Récupère la liste du personnel Gésic*/
    public function findEquipeSupport(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role IN (:roles)')
            ->setParameter('roles', [Role::DEVELOPPEUR, Role::ADMIN])
            ->andWhere('u.topActif = true')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findByClient(int $idClient): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.client = :client')
            ->andWhere('u.topActif = :actif')
            ->setParameter('client', $idClient)
            ->setParameter('actif', true)
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les développeurs avec le nombre de tickets en cours qu'ils traitent
     */
    public function findDeveloppeurAvecCharge(): array
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'COUNT(t.id) AS nbTicketsEnCours')
            ->leftJoin('u.ticketsAssignes', 't')
            ->leftJoin('t.statut', 's')
            ->andWhere('u.role = :role')
            ->andWhere('u.topActif = :actif')
            ->andWhere('s.libelle = :statut OR t.id IS NULL')
            ->setParameter('role', Role::DEVELOPPEUR)
            ->setParameter('actif', true)
            ->setParameter('statut', 'En cours')
            ->groupBy('u.id')
            ->orderBy('nbTicketsEnCours', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne le QueryBuilder pour le formulaire (filtré sur le nom de l'entreprise)
     */
    public function createFindByEntrepriseQueryBuilder(string $nomEntreprise): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->join('u.client', 'c')
            ->where('c.raisonSocial = :entreprise')
            ->andWhere('u.topActif = :actif')
            ->setParameter('entreprise', $nomEntreprise)
            ->setParameter('actif', true)
            ->orderBy('u.nom', 'ASC');
    }
}
