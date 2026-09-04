<?php

namespace App\Tests\Entity;

use App\Entity\Priorite;
use App\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class PrioriteTest extends TestCase
{
    private function createPriorite(): Priorite
    {
        $priorite = new Priorite();
        $priorite->setLibelle('Haute');
        $priorite->setNiveauCriticite(3);

        return $priorite;
    }

    public function testGetterSetterLibelle(): void
    {
        $priorite = $this->createPriorite();

        $this->assertSame('Haute', $priorite->getLibelle());
    }

    public function testGetterSetterNiveauCriticite(): void
    {
        $priorite = $this->createPriorite();

        $this->assertSame(3, $priorite->getNiveauCriticite());
    }

    public function testGetterSetterDescription(): void
    {
        $priorite = $this->createPriorite();
        $priorite->setDescription('Traitement sous 24h');

        $this->assertSame('Traitement sous 24h', $priorite->getDescription());
    }

    public function testDescriptionEstNullableParDefaut(): void
    {
        $priorite = $this->createPriorite();

        $this->assertNull($priorite->getDescription());
    }

    public function testGetTicketsRetourneUneCollectionVideParDefaut(): void
    {
        $priorite = $this->createPriorite();

        $this->assertCount(0, $priorite->getTickets());
    }

    public function testAjoutDeTicketDansLaCollection(): void
    {
        $priorite = $this->createPriorite();
        $ticket = new Ticket();

        $priorite->getTickets()->add($ticket);

        $this->assertCount(1, $priorite->getTickets());
        $this->assertTrue($priorite->getTickets()->contains($ticket));
    }
}
