<?php

namespace App\Entity;

use App\Enum\StatutTicket;
use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

// 1. DÉCLARATION DE L'ENTITÉ & CONFIGURATION DE LA TABLE
#[ORM\Entity(repositoryClass: TicketRepository::class)] // Indique à Doctrine que cette classe est une entité BDD et lui associe son Repository
#[ORM\Table(name: 'ticket')] // Spécifie le nom exact de la table MySQL/MariaDB
class Ticket
{
    // 2. CLÉ PRIMAIRE ET AUTO-INCREMENT
    #[ORM\Id] // Définit la propriété comme clé primaire
    #[ORM\GeneratedValue] // Active l'auto-incrément (1, 2, 3...)
    #[ORM\Column(name: 'id_ticket', type: 'integer')] // Mappe la propriété vers la colonne 'id_ticket' de type INT
    private ?int $id = null;

    // 3. CHAMPS DU FORMULAIRE (Titre & Description)
    #[ORM\Column(name: 'titre', type: 'string', length: 200)] // Colonne VARCHAR(200)
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')] // Validation : le champ ne peut pas être vide
    #[Assert\Length( // Validation : contraintes de longueur minimale et maximale
        min: 5,
        max: 200,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.'
    )]
    private string $titre;

    #[ORM\Column(name: 'description', type: 'text')] // Colonne TEXT (pour des contenus longs)
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'La description doit faire au moins {{ limit }} caractères pour être explicite.'
    )]
    private string $description;

    // 4. RELATIONS UTILISATEURS (3 rôles différents pointant vers la même entité Utilisateur)
    // Relation : Créateur (Obligatoire)
    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'ticketsCrees')] // Un utilisateur peut créer plusieurs tickets
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_user', nullable: false)] // Clé étrangère 'id_utilisateur' NOT NULL
    #[Assert\NotNull(message: 'Le créateur du ticket doit être renseigné.')]
    private Utilisateur $createur;

    // Relation : Développeur/Support Assigné (Optionnel)
    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'ticketsAssignes')]
    #[ORM\JoinColumn(name: 'id_assigne', referencedColumnName: 'id_user', nullable: true)] // Clé étrangère 'id_assigne' NULLABLE
    private ?Utilisateur $assigne = null;

    // Relation : Destinataire / Interlocuteur cible (Optionnel)
    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'ticketsDestines')]
    #[ORM\JoinColumn(name: 'id_destinataire', referencedColumnName: 'id_user', nullable: true)] // Clé étrangère 'id_destinataire' NULLABLE
    private ?Utilisateur $destinataire = null;

    // 5. RÈGLES MÉTIER ET PRIORISATION
    #[ORM\ManyToOne(targetEntity: Impact::class, inversedBy: 'tickets')] // Clé étrangère vers la table 'impact'
    #[ORM\JoinColumn(name: 'id_impact', referencedColumnName: 'id_impact', nullable: false)]
    #[Assert\NotNull(message: 'Veuillez sélectionner un niveau d\'impact.')]
    private Impact $impact;

    #[ORM\ManyToOne(targetEntity: Urgence::class, inversedBy: 'tickets')] // Clé étrangère vers la table 'urgence'
    #[ORM\JoinColumn(name: 'id_urgence', referencedColumnName: 'id_urgence', nullable: false)]
    #[Assert\NotNull(message: 'Veuillez sélectionner un niveau d\'urgence.')]
    private Urgence $urgence;

    #[ORM\Column(name: 'priorite_calculee', type: 'smallint')] // Colonne SMALLINT pour stocker le score (Note Impact x Note Urgence)
    #[Assert\PositiveOrZero(message: 'La priorité calculée doit être positive ou nulle.')]
    private int $prioriteCalculee = 0;

    #[ORM\Column(name: 'statut', type: 'string', enumType: StatutTicket::class)] // Mappe un Enum PHP 8 vers une colonne String
    #[Assert\NotNull(message: 'Le statut du ticket est obligatoire.')]
    private StatutTicket $statut = StatutTicket::NOUVEAU;

    #[ORM\ManyToOne(targetEntity: LogicielClient::class, inversedBy: 'tickets')] // Clé étrangère vers le logiciel du client concerné
    #[ORM\JoinColumn(name: 'id_logiciel_client', referencedColumnName: 'id_client_logiciel', nullable: false)]
    #[Assert\NotNull(message: 'Veuillez sélectionner le logiciel concerné.')]
    private LogicielClient $logicielClient;

    // 6. GESTION DES DATES
    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')] // Date non modifiable après création
    #[Assert\NotNull]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'date_cloture', type: 'datetime', nullable: true)] // Date de clôture remplie uniquement à la résolution
    #[Assert\GreaterThan( // Validation : la date de clôture doit être strictement postérieure à la date de création
        propertyPath: 'dateCreation',
        message: 'La date de clôture doit être supérieure à la date de création.'
    )]
    private ?\DateTimeInterface $dateCloture = null;

    // 7. RELATIONS EN CASCADE (OneToMany / OneToOne)
    // Supression en cascade : si le ticket est supprimé, ses messages associés le sont aussi
    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Message::class, cascade: ['remove'])]
    private Collection $messages;

    // Supression en cascade des historiques associés
    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: HistoriqueStatutTicket::class, cascade: ['remove'])]
    private Collection $historiques;

    // Relation 1-à-1 avec le compte-rendu de résolution
    #[ORM\OneToOne(mappedBy: 'ticket', targetEntity: CompteRendu::class, cascade: ['remove'])]
    #[Assert\Valid] // Validation : déclenche la validation des règles situées dans l'entité CompteRendu
    private ?CompteRendu $compteRendu = null;

    // Relation 1-à-plusieurs vers les tâches de travail liées à ce ticket
    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Tache::class)]
    private Collection $taches;
    /**
     * Undocumented function
     */
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

    public function getLibellePriorite(): string
    {
        if ($this->urgence->getNote() >= 17) {
            return 'URGENTE';
        }

        return match (true) {
            $this->prioriteCalculee >= 17 => 'URGENTE',
            $this->prioriteCalculee >= 12 => 'HAUTE',
            $this->prioriteCalculee >= 8  => 'NORMALE',
            $this->prioriteCalculee >= 3  => 'BASSE',
            default                       => 'TRÈS BASSE',
        };
    }

    public function getStatut(): StatutTicket
    {
        return $this->statut;
    }
    public function setStatut(StatutTicket $s): static
    {
        $this->statut = $s;
        if ($s === StatutTicket::NOUVEAU) {
            $this->assigne = null;
        }
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
