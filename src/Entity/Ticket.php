<?php

namespace App\Entity;

use App\Enum\NiveauImpact;
use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'ticket')]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_ticket', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'titre', type: 'string', length: 200)]
    private string $titre;

    #[ORM\Column(name: 'description', type: 'text')]
    private string $description;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'ticketsCrees')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_user', nullable: false)]
    private Utilisateur $createur;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'ticketsAssignes')]
    #[ORM\JoinColumn(name: 'id_assigne', referencedColumnName: 'id_user', nullable: true)]
    private ?Utilisateur $assigne = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'ticketsDestines')]
    #[ORM\JoinColumn(name: 'id_destinataire', referencedColumnName: 'id_user', nullable: true)]
    private ?Utilisateur $destinataire = null;

    #[ORM\ManyToOne(targetEntity: Priorite::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'id_priorite', referencedColumnName: 'id_priorite', nullable: false)]
    private Priorite $priorite;

    #[ORM\Column(name: 'note_priorite', type: 'integer')]
    private int $notePriorite = 0;

    #[ORM\ManyToOne(targetEntity: StatutTicket::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'id_statut', referencedColumnName: 'id_statut', nullable: false)]
    private StatutTicket $statut;

    #[ORM\ManyToOne(targetEntity: LogicielClient::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'id_logiciel_client', referencedColumnName: 'id_client_logiciel', nullable: false)]
    private LogicielClient $logicielClient;

    #[ORM\ManyToOne(targetEntity: CategorieTicket::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'id_categorie', referencedColumnName: 'id_categorie', nullable: false)]
    private CategorieTicket $categorie;

    #[ORM\Column(name: 'niveau_impact', type: 'string', enumType: NiveauImpact::class)]
    private NiveauImpact $niveauImpact = NiveauImpact::ISOLE;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'date_cloture', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateCloture = null;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Message::class, cascade: ['remove'])]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: HistoriqueStatutTicket::class, cascade: ['remove'])]
    private Collection $historiques;

    #[ORM\OneToOne(mappedBy: 'ticket', targetEntity: CompteRendu::class, cascade: ['remove'])]
    private ?CompteRendu $compteRendu = null;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Tache::class)]
    private Collection $taches;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
        $this->messages     = new ArrayCollection();
        $this->historiques  = new ArrayCollection();
        $this->taches       = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitre(): string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getCreateur(): Utilisateur { return $this->createur; }
    public function setCreateur(Utilisateur $createur): static { $this->createur = $createur; return $this; }

    public function getAssigne(): ?Utilisateur { return $this->assigne; }
    public function setAssigne(?Utilisateur $assigne): static { $this->assigne = $assigne; return $this; }

    public function getDestinataire(): ?Utilisateur { return $this->destinataire; }
    public function setDestinataire(?Utilisateur $destinataire): static { $this->destinataire = $destinataire; return $this; }

    public function getPriorite(): Priorite { return $this->priorite; }
    public function setPriorite(Priorite $priorite): static { $this->priorite = $priorite; return $this; }

    public function getNotePriorite(): int { return $this->notePriorite; }
    public function setNotePriorite(int $note): static { $this->notePriorite = $note; return $this; }

    public function getStatut(): StatutTicket { return $this->statut; }
    public function setStatut(StatutTicket $statut): static { $this->statut = $statut; return $this; }

    public function getLogicielClient(): LogicielClient { return $this->logicielClient; }
    public function setLogicielClient(LogicielClient $logicielClient): static { $this->logicielClient = $logicielClient; return $this; }

    public function getCategorie(): CategorieTicket { return $this->categorie; }
    public function setCategorie(CategorieTicket $categorie): static { $this->categorie = $categorie; return $this; }

    public function getNiveauImpact(): NiveauImpact { return $this->niveauImpact; }
    public function setNiveauImpact(NiveauImpact $niveauImpact): static { $this->niveauImpact = $niveauImpact; return $this; }

    public function getDateCreation(): \DateTimeImmutable { return $this->dateCreation; }

    public function getDateCloture(): ?\DateTimeInterface { return $this->dateCloture; }
    public function setDateCloture(?\DateTimeInterface $date): static { $this->dateCloture = $date; return $this; }

    public function getMessages(): Collection { return $this->messages; }
    public function getHistoriques(): Collection { return $this->historiques; }
    public function getCompteRendu(): ?CompteRendu { return $this->compteRendu; }
    public function getTaches(): Collection { return $this->taches; }
}
