<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'client')]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_client', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'raison_social', type: 'string', length: 150)]
    private string $raisonSocial;

    #[ORM\Column(name: 'adresse', type: 'string', length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(name: 'ville', type: 'string', length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(name: 'code_postal', type: 'string', length: 10, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(name: 'num_tel', type: 'string', length: 20, nullable: true)]
    private ?string $numTel = null;

    #[ORM\Column(name: 'email', type: 'string', length: 150, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'positionX', type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?float $positionX = null;

    #[ORM\Column(name: 'positionY', type: 'decimal', precision: 10, scale: 7, nullable: true)]
    private ?float $positionY = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\Column(name: 'top_actif', type: 'boolean')]
    private bool $topActif = true;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Utilisateur::class)]
    private Collection $utilisateurs;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: LogicielClient::class)]
    private Collection $logicielsClient;

    public function __construct()
    {
        $this->dateCreation    = new \DateTimeImmutable();
        $this->utilisateurs    = new ArrayCollection();
        $this->logicielsClient = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getRaisonSocial(): string { return $this->raisonSocial; }
    public function setRaisonSocial(string $raisonSocial): static { $this->raisonSocial = $raisonSocial; return $this; }

    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(?string $adresse): static { $this->adresse = $adresse; return $this; }

    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): static { $this->ville = $ville; return $this; }

    public function getCodePostal(): ?string { return $this->codePostal; }
    public function setCodePostal(?string $codePostal): static { $this->codePostal = $codePostal; return $this; }

    public function getNumTel(): ?string { return $this->numTel; }
    public function setNumTel(?string $numTel): static { $this->numTel = $numTel; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): static { $this->email = $email; return $this; }

    public function getPositionX(): ?float { return $this->positionX; }
    public function setPositionX(?float $positionX): static { $this->positionX = $positionX; return $this; }

    public function getPositionY(): ?float { return $this->positionY; }
    public function setPositionY(?float $positionY): static { $this->positionY = $positionY; return $this; }

    public function getDateCreation(): \DateTimeImmutable { return $this->dateCreation; }

    public function isTopActif(): bool { return $this->topActif; }
    public function setTopActif(bool $topActif): static { $this->topActif = $topActif; return $this; }

    public function getUtilisateurs(): Collection { return $this->utilisateurs; }
    public function getLogicielsClient(): Collection { return $this->logicielsClient; }
}
