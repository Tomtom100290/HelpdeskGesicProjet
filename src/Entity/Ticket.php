<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Enum\StatutTicket;
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

    #[ORM\ManyToOne(targetEntity: Impact::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'id_impact', referencedColumnName: 'id_impact', nullable: false)]
    private Impact $impact;

    #[ORM\ManyToOne(targetEntity: Urgence::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'id_urgence', referencedColumnName: 'id_urgence', nullable: false)]
    private Urgence $urgence;

    #[ORM\Column(name: 'priorite_calculee', type: 'smallint')]
    private int $prioriteCalculee = 0;

    // ✅ Enum au lieu de ManyToOne
    #[ORM\Column(name: 'statut', type: 'string', enumType: StatutTicket::class)]
    private StatutTicket $statut = StatutTicket::NOUVEAU;

    #[ORM\ManyToOne(targetEntity: LogicielClient::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(name: 'id_logiciel_client', referencedColumnName: 'id_client_logiciel', nullable: false)]
    private LogicielClient $logicielClient;

    //#[ORM\ManyToOne(targetEntity: CategorieTicket::class, inversedBy: 'tickets')]
    // #[ORM\JoinColumn(name: 'id_categorie', referencedColumnName: 'id_categorie', nullable: false)]
    // private CategorieTicket $categorie;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }
    public function setTitre(string $t): static
    {
        $this->titre = $t;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription(string $d): static
    {
        $this->description = $d;
        return $this;
    }

    public function getCreateur(): Utilisateur
    {
        return $this->createur;
    }
    public function setCreateur(Utilisateur $u): static
    {
        $this->createur = $u;
        return $this;
    }

    public function getAssigne(): ?Utilisateur
    {
        return $this->assigne;
    }
    public function setAssigne(?Utilisateur $u): static
    {
        $this->assigne = $u;
        return $this;
    }

    public function getDestinataire(): ?Utilisateur
    {
        return $this->destinataire;
    }
    public function setDestinataire(?Utilisateur $u): static
    {
        $this->destinataire = $u;
        return $this;
    }

    public function getImpact(): Impact
    {
        return $this->impact;
    }
    public function setImpact(Impact $i): static
    {
        $this->impact = $i;
        return $this;
    }

    public function getUrgence(): Urgence
    {
        return $this->urgence;
    }
    public function setUrgence(Urgence $u): static
    {
        $this->urgence = $u;
        return $this;
    }

    public function getPrioriteCalculee(): int
    {
        return $this->prioriteCalculee;
    }
    public function setPrioriteCalculee(int $p): static
    {
        $this->prioriteCalculee = $p;
        return $this;
    }

    public function getStatut(): StatutTicket
    {
        return $this->statut;
    }
    public function setStatut(StatutTicket $s): static
    {
        $this->statut = $s;
        return $this;
    }

    public function getLogicielClient(): LogicielClient
    {
        return $this->logicielClient;
    }
    public function setLogicielClient(LogicielClient $l): static
    {
        $this->logicielClient = $l;
        return $this;
    }

    //public function getCategorie(): CategorieTicket
    //{
    //    return $this->categorie;
    //}
    //public function setCategorie(CategorieTicket $c): static
    //{
    //    $this->categorie = $c;
    //    return $this;
    //}

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->dateCloture;
    }
    public function setDateCloture(?\DateTimeInterface $d): static
    {
        $this->dateCloture = $d;
        return $this;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }
    public function getCompteRendu(): ?CompteRendu
    {
        return $this->compteRendu;
    }
    public function getTaches(): Collection
    {
        return $this->taches;
    }
}
