<?php

namespace App\Entity;

use App\Enum\StatutTache;
use App\Repository\TacheRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
#[ORM\Table(name: 'tache')]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_tache', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'taches')]
    #[ORM\JoinColumn(name: 'id_ticket', referencedColumnName: 'id_ticket', nullable: true)]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'tachesAssignees')]
    #[ORM\JoinColumn(name: 'id_utilisateur_assign', referencedColumnName: 'id_user', nullable: true)]
    private ?Utilisateur $utilisateurAssigne = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'tachesCrees')]
    #[ORM\JoinColumn(name: 'id_utilisateur_creat', referencedColumnName: 'id_user', nullable: false)]
    private Utilisateur $utilisateurCreateur;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'libelle', type: 'string', length: 200)]
    private string $libelle;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'date_realisation', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateRealisation = null;

    #[ORM\Column(name: 'statut', type: 'string', enumType: StatutTache::class)]
    private StatutTache $statut = StatutTache::A_FAIRE;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }
    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;
        return $this;
    }

    public function getUtilisateurAssigne(): ?Utilisateur
    {
        return $this->utilisateurAssigne;
    }
    public function setUtilisateurAssigne(?Utilisateur $utilisateur): static
    {
        $this->utilisateurAssigne = $utilisateur;
        return $this;
    }

    public function getUtilisateurCreateur(): Utilisateur
    {
        return $this->utilisateurCreateur;
    }
    public function setUtilisateurCreateur(Utilisateur $utilisateur): static
    {
        $this->utilisateurCreateur = $utilisateur;
        return $this;
    }

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateRealisation(): ?\DateTimeInterface
    {
        return $this->dateRealisation;
    }
    public function setDateRealisation(?\DateTimeInterface $date): static
    {
        $this->dateRealisation = $date;
        return $this;
    }

    public function getStatut(): StatutTache
    {
        return $this->statut;
    }
    public function setStatut(StatutTache $statut): static
    {
        $this->statut = $statut;
        return $this;
    }
}
