<?php

namespace App\Entity;

use App\Enum\Role;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'id_client', referencedColumnName: 'id_client', nullable: true)]
    private ?Client $client = null;

    #[ORM\Column(name: 'nom', type: 'string', length: 80)]
    private string $nom;

    #[ORM\Column(name: 'prenom', type: 'string', length: 80)]
    private string $prenom;

    #[ORM\Column(name: 'email', type: 'string', length: 150, unique: true)]
    private string $email;

    #[ORM\Column(name: 'entreprise', type: 'string', length: 150, nullable: true)]
    private ?string $entreprise = null;

    #[ORM\Column(name: 'role', type: 'string', enumType: Role::class)]
    private Role $role;

    #[ORM\Column(name: 'mot_de_passe', type: 'string', length: 255)]
    private string $motDePasse;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'top_actif', type: 'boolean')]
    private bool $topActif = true;

    #[ORM\Column(name: 'num_tel', type: 'string', length: 20, nullable: true)]
    private ?string $numTel = null;

    #[ORM\OneToMany(mappedBy: 'createur', targetEntity: Ticket::class)]
    private Collection $ticketsCrees;

    #[ORM\OneToMany(mappedBy: 'assigne', targetEntity: Ticket::class)]
    private Collection $ticketsAssignes;

    #[ORM\OneToMany(mappedBy: 'destinataire', targetEntity: Ticket::class)]
    private Collection $ticketsDestines;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: HistoriqueStatutTicket::class)]
    private Collection $historiques;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: CompteRendu::class)]
    private Collection $comptesRendus;

    #[ORM\OneToMany(mappedBy: 'utilisateurAssigne', targetEntity: Tache::class)]
    private Collection $tachesAssignees;

    #[ORM\OneToMany(mappedBy: 'utilisateurCreateur', targetEntity: Tache::class)]
    private Collection $tachesCrees;

    public function __construct()
    {
        $this->dateCreation    = new \DateTimeImmutable();
        $this->ticketsCrees    = new ArrayCollection();
        $this->ticketsAssignes = new ArrayCollection();
        $this->ticketsDestines = new ArrayCollection();
        $this->messages        = new ArrayCollection();
        $this->historiques     = new ArrayCollection();
        $this->comptesRendus   = new ArrayCollection();
        $this->tachesAssignees = new ArrayCollection();
        $this->tachesCrees     = new ArrayCollection();
    }

    // --- UserInterface ---

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return ['ROLE_' . strtoupper($this->role->value)];
    }

    public function eraseCredentials(): void {}

    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    // --- Getters / Setters ---

    public function getId(): ?int { return $this->id; }

    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): static { $this->client = $client; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getEntreprise(): ?string { return $this->entreprise; }
    public function setEntreprise(?string $entreprise): static { $this->entreprise = $entreprise; return $this; }

    public function getRole(): Role { return $this->role; }
    public function setRole(Role $role): static { $this->role = $role; return $this; }

    public function getMotDePasse(): string { return $this->motDePasse; }
    public function setMotDePasse(string $motDePasse): static { $this->motDePasse = $motDePasse; return $this; }

    public function getDateCreation(): \DateTimeImmutable { return $this->dateCreation; }

    public function isTopActif(): bool { return $this->topActif; }
    public function setTopActif(bool $topActif): static { $this->topActif = $topActif; return $this; }

    public function getNumTel(): ?string { return $this->numTel; }
    public function setNumTel(?string $numTel): static { $this->numTel = $numTel; return $this; }

    public function getTicketsCrees(): Collection { return $this->ticketsCrees; }
    public function getTicketsAssignes(): Collection { return $this->ticketsAssignes; }
    public function getTicketsDestines(): Collection { return $this->ticketsDestines; }
    public function getMessages(): Collection { return $this->messages; }
    public function getHistoriques(): Collection { return $this->historiques; }
    public function getComptesRendus(): Collection { return $this->comptesRendus; }
    public function getTachesAssignees(): Collection { return $this->tachesAssignees; }
    public function getTachesCrees(): Collection { return $this->tachesCrees; }
}
