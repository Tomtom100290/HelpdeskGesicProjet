<?php

namespace App\Entity;

use App\Repository\StatutTicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatutTicketRepository::class)]
#[ORM\Table(name: 'statut_ticket')]
class StatutTicket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_statut', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'libelle', type: 'string', length: 50)]
    private string $libelle;

    #[ORM\Column(name: 'couleur_lib', type: 'string', length: 7)]
    private string $couleurLib = '#CCCCCC';

    #[ORM\Column(name: 'top_actif', type: 'boolean')]
    private bool $topActif = true;

    #[ORM\OneToMany(mappedBy: 'statut', targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\OneToMany(mappedBy: 'statutAvant', targetEntity: HistoriqueStatutTicket::class)]
    private Collection $historiquesAvant;

    #[ORM\OneToMany(mappedBy: 'statutApres', targetEntity: HistoriqueStatutTicket::class)]
    private Collection $historiquesApres;

    public function __construct()
    {
        $this->tickets          = new ArrayCollection();
        $this->historiquesAvant = new ArrayCollection();
        $this->historiquesApres = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getLibelle(): string { return $this->libelle; }
    public function setLibelle(string $libelle): static { $this->libelle = $libelle; return $this; }

    public function getCouleurLib(): string { return $this->couleurLib; }
    public function setCouleurLib(string $couleur): static { $this->couleurLib = $couleur; return $this; }

    public function isTopActif(): bool { return $this->topActif; }
    public function setTopActif(bool $topActif): static { $this->topActif = $topActif; return $this; }

    public function getTickets(): Collection { return $this->tickets; }
    public function getHistoriquesAvant(): Collection { return $this->historiquesAvant; }
    public function getHistoriquesApres(): Collection { return $this->historiquesApres; }
}
