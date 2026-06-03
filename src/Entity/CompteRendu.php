<?php

namespace App\Entity;

use App\Repository\CompteRenduRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteRenduRepository::class)]
#[ORM\Table(name: 'compte_rendu')]
class CompteRendu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_compte_rendu', type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Ticket::class, inversedBy: 'compteRendu')]
    #[ORM\JoinColumn(name: 'id_ticket', referencedColumnName: 'id_ticket', nullable: false)]
    private Ticket $ticket;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'comptesRendus')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_user', nullable: false)]
    private Utilisateur $utilisateur;

    #[ORM\Column(name: 'contenu', type: 'text')]
    private string $contenu;

    #[ORM\Column(name: 'temps_traitement_minutes', type: 'integer', nullable: true)]
    private ?int $tempsTraitementMinutes = null;

    #[ORM\Column(name: 'date_redaction', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateRedaction;

    public function __construct()
    {
        $this->dateRedaction = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTicket(): Ticket { return $this->ticket; }
    public function setTicket(Ticket $ticket): static { $this->ticket = $ticket; return $this; }

    public function getUtilisateur(): Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getContenu(): string { return $this->contenu; }
    public function setContenu(string $contenu): static { $this->contenu = $contenu; return $this; }

    public function getTempsTraitementMinutes(): ?int { return $this->tempsTraitementMinutes; }
    public function setTempsTraitementMinutes(?int $temps): static { $this->tempsTraitementMinutes = $temps; return $this; }

    public function getDateRedaction(): \DateTimeImmutable { return $this->dateRedaction; }
}
