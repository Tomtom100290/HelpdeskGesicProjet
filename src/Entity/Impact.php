<?php

namespace App\Entity;

use App\Repository\ImpactRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImpactRepository::class)]
#[ORM\Table(name: 'impact')]
class Impact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_impact', type: 'integer')]
    private ?int $id = null;

    /** Niveau d'impact */
    #[ORM\Column(name: 'niveau', type: 'string', length: 10, unique: true)]
    private string $niveau;

    #[ORM\Column(name: 'libelle', type: 'string', length: 100)]
    private string $libelle;

    #[ORM\Column(name: 'prompt', type: 'text')]
    private string $prompt;

    #[ORM\Column(name: 'note', type: 'smallint')]
    private int $note;

    #[ORM\OneToMany(mappedBy: 'impact', targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiveau(): string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;
        return $this;
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

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function setPrompt(string $prompt): static
    {
        $this->prompt = $prompt;
        return $this;
    }

    public function getNote(): int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        if ($note < 1 || $note > 4) {
            throw new \InvalidArgumentException('La note doit être comprise entre 1 et 4.');
        }
        $this->note = $note;
        return $this;
    }

    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function __toString(): string
    {
        return sprintf('%s — %s', $this->niveau, $this->libelle);
    }
}
