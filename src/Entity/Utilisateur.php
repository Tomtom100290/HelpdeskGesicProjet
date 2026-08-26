<?php

namespace App\Entity;

use App\Enum\Role;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Entity\CompteRendu;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
#[Vich\Uploadable]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_user', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name: 'id_client', referencedColumnName: 'id_client', nullable: true)]
    private ?Client $client = null;

    // On stocke uniquement le NOM du fichier en base, pas l'image elle-même
    #[ORM\Column(name: 'image_profil', type: 'string', length: 80, nullable: true)]
    private ?string $imageProfil = null;

    #[ORM\Column(name: 'image_profil_size', type: 'integer', nullable: true)]
    private ?int $imageProfilSize = null;

    // Champ NON mappé en base : Vich s'en sert pour gérer l'upload du fichier physique
    #[Vich\UploadableField(mapping: 'user_profile', fileNameProperty: 'imageProfil', size: 'imageProfilSize')]
    private ?File $imageProfilFile = null;

    // Obligatoire pour que Doctrine détecte le changement et déclenche l'upload Vich
    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(name: 'nom', type: 'string', length: 80)]
    private string $nom;

    #[ORM\Column(name: 'prenom', type: 'string', length: 80)]
    private string $prenom;

    #[ORM\Column(name: 'email', type: 'string', length: 150, unique: true)]
    private string $email;

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

    /**
     * Méthode requise par UserInterface pour la sécurité Symfony
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = [];

        if (isset($this->role)) {
            $roles[] = $this->role->value;
        }

        // On garantit que tout utilisateur connecté a au moins ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function eraseCredentials(): void {}

    // --- Sérialisation (exclut les propriétés non sérialisables comme File) ---

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'role' => $this->role ?? null,
            'motDePasse' => $this->motDePasse,
            'dateCreation' => $this->dateCreation,
            'topActif' => $this->topActif,
            'numTel' => $this->numTel,
            'imageProfil' => $this->imageProfil,
            'imageProfilSize' => $this->imageProfilSize,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->nom = $data['nom'];
        $this->prenom = $data['prenom'];
        $this->email = $data['email'];
        if ($data['role'] !== null) {
            $this->role = $data['role'];
        }
        $this->motDePasse = $data['motDePasse'];
        $this->dateCreation = $data['dateCreation'];
        $this->topActif = $data['topActif'];
        $this->numTel = $data['numTel'];
        $this->imageProfil = $data['imageProfil'];
        $this->imageProfilSize = $data['imageProfilSize'];
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    // --- Getters / Setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }
    public function setClient(?Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getImageProfil(): ?string
    {
        return $this->imageProfil;
    }
    public function setImageProfil(?string $imageProfil): static
    {
        $this->imageProfil = $imageProfil;
        return $this;
    }

    public function getImageProfilSize(): ?int
    {
        return $this->imageProfilSize;
    }
    public function setImageProfilSize(?int $imageProfilSize): static
    {
        $this->imageProfilSize = $imageProfilSize;
        return $this;
    }

    public function getImageProfilFile(): ?File
    {
        return $this->imageProfilFile;
    }

    public function setImageProfilFile(?File $imageProfilFile = null): static
    {
        $this->imageProfilFile = $imageProfilFile;

        if (null !== $imageProfilFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }
    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role ?? null;
    }
    public function setRole(Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }
    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function isTopActif(): bool
    {
        return $this->topActif;
    }
    public function setTopActif(bool $topActif): static
    {
        $this->topActif = $topActif;
        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->numTel;
    }
    public function setNumTel(?string $numTel): static
    {
        $this->numTel = $numTel;
        return $this;
    }

    public function getTicketsCrees(): Collection
    {
        return $this->ticketsCrees;
    }
    public function getTicketsAssignes(): Collection
    {
        return $this->ticketsAssignes;
    }
    public function getTicketsDestines(): Collection
    {
        return $this->ticketsDestines;
    }
    public function getMessages(): Collection
    {
        return $this->messages;
    }
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }
    public function getComptesRendus(): Collection
    {
        return $this->comptesRendus;
    }
    public function getTachesAssignees(): Collection
    {
        return $this->tachesAssignees;
    }
    public function getTachesCrees(): Collection
    {
        return $this->tachesCrees;
    }
}
