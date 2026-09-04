<?php

namespace App\Tests\Entity;

use App\Entity\HistoriqueStatutTicket;
use App\Entity\StatutTicket;
use App\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class StatutTicketTest extends TestCase
{
    private function createStatutTicket(): StatutTicket
    {
        $statut = new StatutTicket();
        $statut->setLibelle('Nouveau');

        return $statut;
    }

    public function testGetterSetterLibelle(): void
    {
        $statut = $this->createStatutTicket();

        $this->assertSame('Nouveau', $statut->getLibelle());
    }

    public function testCouleurLibValeurParDefaut(): void
    {
        $statut = $this->createStatutTicket();

        $this->assertSame('#CCCCCC', $statut->getCouleurLib());
    }

    public function testGetterSetterCouleurLib(): void
    {
        $statut = $this->createStatutTicket();
        $statut->setCouleurLib('#FF0000');

        $this->assertSame('#FF0000', $statut->getCouleurLib());
    }

    public function testStatutEstActifParDefaut(): void
    {
        $statut = $this->createStatutTicket();

        $this->assertTrue($statut->isTopActif());
    }

    public function testGetterSetterTopActif(): void
    {
        $statut = $this->createStatutTicket();
        $statut->setTopActif(false);

        $this->assertFalse($statut->isTopActif());
    }

    public function testGetTicketsRetourneUneCollectionVideParDefaut(): void
    {
        $statut = $this->createStatutTicket();

        $this->assertCount(0, $statut->getTickets());
    }

    public function testAjoutDeTicketDansLaCollection(): void
    {
        $statut = $this->createStatutTicket();
        $ticket = new Ticket();

        $statut->getTickets()->add($ticket);

        $this->assertCount(1, $statut->getTickets());
        $this->assertTrue($statut->getTickets()->contains($ticket));
    }

    public function testGetHistoriquesAvantRetourneUneCollectionVideParDefaut(): void
    {
        $statut = $this->createStatutTicket();

        $this->assertCount(0, $statut->getHistoriquesAvant());
    }

    public function testAjoutDHistoriqueAvantDansLaCollection(): void
    {
        $statut = $this->createStatutTicket();
        $historique = new HistoriqueStatutTicket();

        $statut->getHistoriquesAvant()->add($historique);

        $this->assertCount(1, $statut->getHistoriquesAvant());
        $this->assertTrue($statut->getHistoriquesAvant()->contains($historique));
    }

    public function testGetHistoriquesApresRetourneUneCollectionVideParDefaut(): void
    {
        $statut = $this->createStatutTicket();

        $this->assertCount(0, $statut->getHistoriquesApres());
    }

    public function testAjoutDHistoriqueApresDansLaCollection(): void
    {
        $statut = $this->createStatutTicket();
        $historique = new HistoriqueStatutTicket();

        $statut->getHistoriquesApres()->add($historique);

        $this->assertCount(1, $statut->getHistoriquesApres());
        $this->assertTrue($statut->getHistoriquesApres()->contains($historique));
    }
}
