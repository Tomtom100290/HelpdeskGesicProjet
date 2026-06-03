<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'message')]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_message', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'id_ticket', referencedColumnName: 'id_ticket', nullable: false)]
    private Ticket $ticket;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_user', nullable: false)]
    private Utilisateur $utilisateur;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'reponses')]
    #[ORM\JoinColumn(name: 'id_message_parent', referencedColumnName: 'id_message', nullable: true)]
    private ?Message $messageParent = null;

    #[ORM\OneToMany(mappedBy: 'messageParent', targetEntity: self::class)]
    private Collection $reponses;

    #[ORM\Column(name: 'contenu', type: 'text')]
    private string $contenu;

    #[ORM\Column(name: 'top_actif', type: 'boolean')]
    private bool $topActif = true;

    #[ORM\Column(name: 'date_envoi', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateEnvoi;

    public function __construct()
    {
        $this->dateEnvoi = new \DateTimeImmutable();
        $this->reponses  = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTicket(): Ticket { return $this->ticket; }
    public function setTicket(Ticket $ticket): static { $this->ticket = $ticket; return $this; }

    public function getUtilisateur(): Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getMessageParent(): ?Message { return $this->messageParent; }
    public function setMessageParent(?Message $message): static { $this->messageParent = $message; return $this; }

    public function getReponses(): Collection { return $this->reponses; }

    public function getContenu(): string { return $this->contenu; }
    public function setContenu(string $contenu): static { $this->contenu = $contenu; return $this; }

    public function isTopActif(): bool { return $this->topActif; }
    public function setTopActif(bool $topActif): static { $this->topActif = $topActif; return $this; }

    public function getDateEnvoi(): \DateTimeImmutable { return $this->dateEnvoi; }
}
