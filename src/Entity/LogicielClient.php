<?php

namespace App\Entity;

use App\Repository\LogicielClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogicielClientRepository::class)]
#[ORM\Table(name: 'logiciel_client')]
class LogicielClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_client_logiciel', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'logicielsClient')]
    #[ORM\JoinColumn(name: 'id_client', referencedColumnName: 'id_client', nullable: false)]
    private Client $client;

    #[ORM\ManyToOne(targetEntity: Logiciel::class, inversedBy: 'logicielsClient')]
    #[ORM\JoinColumn(name: 'id_logiciel', referencedColumnName: 'id_logiciel', nullable: false)]
    private Logiciel $logiciel;

    #[ORM\Column(name: 'date_installation', type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateInstallation = null;

    #[ORM\Column(name: 'version_logiciel', type: 'string', length: 20, nullable: true)]
    private ?string $versionLogiciel = null;

    #[ORM\Column(name: 'notes', type: 'text', nullable: true)] //notes = Textes qui permet d'ajouter des précisions sur l'installation du logiciel chez le client
    private ?string $notes = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateCreation;

    #[ORM\OneToMany(mappedBy: 'logicielClient', targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
        $this->tickets      = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }
    public function setClient(Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getLogiciel(): Logiciel
    {
        return $this->logiciel;
    }
    public function setLogiciel(Logiciel $logiciel): static
    {
        $this->logiciel = $logiciel;
        return $this;
    }

    public function getDateInstallation(): ?\DateTimeInterface
    {
        return $this->dateInstallation;
    }
    public function setDateInstallation(?\DateTimeInterface $date): static
    {
        $this->dateInstallation = $date;
        return $this;
    }

    public function getVersionLogiciel(): ?string
    {
        return $this->versionLogiciel;
    }
    public function setVersionLogiciel(?string $version): static
    {
        $this->versionLogiciel = $version;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function getTickets(): Collection
    {
        return $this->tickets;
    }
}
