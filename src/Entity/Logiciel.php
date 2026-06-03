<?php

namespace App\Entity;

use App\Repository\LogicielRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogicielRepository::class)]
#[ORM\Table(name: 'logiciel')]
class Logiciel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_logiciel', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'libelle', type: 'string', length: 100)]
    private string $libelle;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'fk_type_logiciel', type: 'string', length: 80, nullable: true)]
    private ?string $typeLogiciel = null;

    #[ORM\Column(name: 'coeff_criticite', type: 'decimal', precision: 3, scale: 1)]
    private float $coeffCriticite = 1.0;

    #[ORM\Column(name: 'top_actif', type: 'boolean')]
    private bool $topActif = true;

    #[ORM\OneToMany(mappedBy: 'logiciel', targetEntity: LogicielClient::class)]
    private Collection $logicielsClient;

    public function __construct()
    {
        $this->logicielsClient = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getLibelle(): string { return $this->libelle; }
    public function setLibelle(string $libelle): static { $this->libelle = $libelle; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getTypeLogiciel(): ?string { return $this->typeLogiciel; }
    public function setTypeLogiciel(?string $typeLogiciel): static { $this->typeLogiciel = $typeLogiciel; return $this; }

    public function getCoeffCriticite(): float { return $this->coeffCriticite; }
    public function setCoeffCriticite(float $coeffCriticite): static { $this->coeffCriticite = $coeffCriticite; return $this; }

    public function isTopActif(): bool { return $this->topActif; }
    public function setTopActif(bool $topActif): static { $this->topActif = $topActif; return $this; }

    public function getLogicielsClient(): Collection { return $this->logicielsClient; }
}
