<?php

namespace App\Entity;

use App\Repository\CategorieTicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieTicketRepository::class)]
#[ORM\Table(name: 'categorie_ticket')]
class CategorieTicket
//Catégorie ticket permet spécifié le type de demande : Bug, Demande d'évolution, Question, Installation, Blocage logiciel...
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_categorie', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'libelle', type: 'string', length: 150)]
    private string $libelle;

    #[ORM\Column(name: 'valeur_bloquant', type: 'integer')]
    private int $valeurBloquant = 1;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }
    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getValeurBloquant(): int
    {
        return $this->valeurBloquant;
    }
    public function setValeurBloquant(int $valeur): static
    {
        $this->valeurBloquant = $valeur;
        return $this;
    }

    public function getTickets(): Collection
    {
        return $this->tickets;
    }
}
