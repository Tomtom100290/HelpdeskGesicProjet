<?php

namespace App\Tests\Entity;

use App\Entity\Impact;
use App\Entity\Ticket;
use App\Entity\Urgence;
use App\Entity\Utilisateur;
use App\Enum\StatutTicket;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    public function testCalculPriorite(): void
    {
        $ticket = new Ticket();

        $impact = new Impact();
        $impact->setNote(3);

        $urgence = new Urgence();
        $urgence->setNote(2);

        $ticket->setImpact($impact);
        $ticket->setUrgence($urgence);

        // Simulation du calcul : Impact * Urgence
        $score = $ticket->getImpact()->getNote() * $ticket->getUrgence()->getNote();
        $ticket->setPrioriteCalculee($score);

        // Assertion PHPUnit : vérification que 3 * 2 fait bien 6
        $this->assertEquals(6, $ticket->getPrioriteCalculee());
    }

    private function createTicketWithNotes(int $impactNote, int $urgenceNote, int $prioriteCalculee): Ticket
    {
        $impact = new Impact();
        $impact->setNote($impactNote);

        $urgence = new Urgence();
        $urgence->setNote($urgenceNote);

        $ticket = new Ticket();
        $ticket->setImpact($impact);
        $ticket->setUrgence($urgence);
        $ticket->setPrioriteCalculee($prioriteCalculee);

        return $ticket;
    }

    #[DataProvider('prioriteLibelleProvider')]
    public function testLibellePriorite(int $impactNote, int $urgenceNote, int $prioriteCalculee, string $libelleAttendu): void
    {
        $ticket = $this->createTicketWithNotes($impactNote, $urgenceNote, $prioriteCalculee);

        $this->assertSame($libelleAttendu, $ticket->getLibellePriorite());
    }

    public static function prioriteLibelleProvider(): array
    {
        return [
            'priorite urgente score tres eleve' => [4, 4, 16, 'HAUTE'],
            'priorite haute score eleve'        => [4, 3, 12, 'HAUTE'],
            'priorite normale score moyen'      => [2, 4, 8, 'NORMALE'],
            'priorite basse score faible'       => [1, 3, 3, 'BASSE'],
            'priorite tres basse score minimal' => [1, 1, 1, 'TRÈS BASSE'],
        ];
    }

    public function testSetStatutNouveauRetireLAssignation(): void
    {
        $ticket = $this->createTicketWithNotes(1, 1, 1);
        $ticket->setAssigne(new Utilisateur());

        $this->assertNotNull($ticket->getAssigne());

        $ticket->setStatut(StatutTicket::NOUVEAU);

        $this->assertNull($ticket->getAssigne());
    }

    public function testGetterSetterTitreEtDescription(): void
    {
        $ticket = new Ticket();
        $ticket->setTitre('Serveur mail en panne');
        $ticket->setDescription('Impossible de recevoir des mails depuis ce matin');

        $this->assertSame('Serveur mail en panne', $ticket->getTitre());
        $this->assertSame('Impossible de recevoir des mails depuis ce matin', $ticket->getDescription());
    }

    public function testDateCreationEstInitialiseeAutomatiquement(): void
    {
        $ticket = new Ticket();

        $this->assertInstanceOf(\DateTimeImmutable::class, $ticket->getDateCreation());
    }
}
