<?php

namespace App\Entity;

use App\Repository\HistoriqueStatutTicketRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueStatutTicketRepository::class)]
#[ORM\Table(name: 'histo_statut_ticket')]
class HistoriqueStatutTicket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_historique', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'historiques')]
    #[ORM\JoinColumn(name: 'id_ticket', referencedColumnName: 'id_ticket', nullable: false)]
    private Ticket $ticket;

    #[ORM\ManyToOne(targetEntity: StatutTicket::class, inversedBy: 'historiquesAvant')]
    #[ORM\JoinColumn(name: 'id_statut_avt', referencedColumnName: 'id_statut', nullable: false)]
    private StatutTicket $statutAvant;

    #[ORM\ManyToOne(targetEntity: StatutTicket::class, inversedBy: 'historiquesApres')]
    #[ORM\JoinColumn(name: 'id_statut_ap', referencedColumnName: 'id_statut', nullable: false)]
    private StatutTicket $statutApres;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'historiques')]
    #[ORM\JoinColumn(name: 'id_utilisateur', referencedColumnName: 'id_user', nullable: false)]
    private Utilisateur $utilisateur;

    #[ORM\Column(name: 'date_changement', type: 'datetime_immutable')]
    private \DateTimeImmutable $dateChangement;

    #[ORM\Column(name: 'commentaire', type: 'string', length: 255, nullable: true)]
    private ?string $commentaire = null;

    public function __construct()
    {
        $this->dateChangement = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getTicket(): Ticket { return $this->ticket; }
    public function setTicket(Ticket $ticket): static { $this->ticket = $ticket; return $this; }

    public function getStatutAvant(): StatutTicket { return $this->statutAvant; }
    public function setStatutAvant(StatutTicket $statut): static { $this->statutAvant = $statut; return $this; }

    public function getStatutApres(): StatutTicket { return $this->statutApres; }
    public function setStatutApres(StatutTicket $statut): static { $this->statutApres = $statut; return $this; }

    public function getUtilisateur(): Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getDateChangement(): \DateTimeImmutable { return $this->dateChangement; }

    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(?string $commentaire): static { $this->commentaire = $commentaire; return $this; }
}
