<?php

namespace App\Entity;

use App\Repository\PrioriteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrioriteRepository::class)]
#[ORM\Table(name: 'priorite')]
class Priorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_priorite', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'libelle', type: 'string', length: 50)]
    private string $libelle;

    #[ORM\Column(name: 'niveau_criticite', type: 'integer')]
    private int $niveauCriticite;

    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'priorite', targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getLibelle(): string { return $this->libelle; }
    public function setLibelle(string $libelle): static { $this->libelle = $libelle; return $this; }

    public function getNiveauCriticite(): int { return $this->niveauCriticite; }
    public function setNiveauCriticite(int $niveau): static { $this->niveauCriticite = $niveau; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getTickets(): Collection { return $this->tickets; }
}
