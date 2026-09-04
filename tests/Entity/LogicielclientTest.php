<?php

namespace App\Tests\Entity;

use App\Entity\Client;
use App\Entity\Logiciel;
use App\Entity\LogicielClient;
use App\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class LogicielClientTest extends TestCase
{
    private function createLogicielClient(): LogicielClient
    {
        $client = new Client();
        $logiciel = new Logiciel();
        $logiciel->setLibelle('Progiplus');

        $logicielClient = new LogicielClient();
        $logicielClient->setClient($client);
        $logicielClient->setLogiciel($logiciel);

        return $logicielClient;
    }

    public function testGetterSetterClient(): void
    {
        $logicielClient = new LogicielClient();
        $client = new Client();

        $logicielClient->setClient($client);

        $this->assertSame($client, $logicielClient->getClient());
    }

    public function testGetterSetterLogiciel(): void
    {
        $logicielClient = new LogicielClient();
        $logiciel = new Logiciel();
        $logiciel->setLibelle('Progiplus');

        $logicielClient->setLogiciel($logiciel);

        $this->assertSame($logiciel, $logicielClient->getLogiciel());
    }

    public function testGetterSetterVersionLogiciel(): void
    {
        $logicielClient = $this->createLogicielClient();
        $logicielClient->setVersionLogiciel('v2.4.1');

        $this->assertSame('v2.4.1', $logicielClient->getVersionLogiciel());
    }

    public function testVersionLogicielEstNullableParDefaut(): void
    {
        $logicielClient = $this->createLogicielClient();

        $this->assertNull($logicielClient->getVersionLogiciel());
    }

    public function testGetterSetterDateInstallation(): void
    {
        $logicielClient = $this->createLogicielClient();
        $date = new \DateTime('2026-06-01');

        $logicielClient->setDateInstallation($date);

        $this->assertSame($date, $logicielClient->getDateInstallation());
    }

    public function testGetterSetterNotes(): void
    {
        $logicielClient = $this->createLogicielClient();
        $logicielClient->setNotes('Installation validée par le client');

        $this->assertSame('Installation validée par le client', $logicielClient->getNotes());
    }

    public function testDateCreationEstInitialiseeAutomatiquement(): void
    {
        $logicielClient = $this->createLogicielClient();

        $this->assertInstanceOf(\DateTimeImmutable::class, $logicielClient->getDateCreation());
    }

    public function testGetTicketsRetourneUneCollectionVideParDefaut(): void
    {
        $logicielClient = $this->createLogicielClient();

        $this->assertCount(0, $logicielClient->getTickets());
    }

    public function testAjoutDeTicketDansLaCollection(): void
    {
        $logicielClient = $this->createLogicielClient();
        $ticket = new Ticket();

        $logicielClient->getTickets()->add($ticket);

        $this->assertCount(1, $logicielClient->getTickets());
        $this->assertTrue($logicielClient->getTickets()->contains($ticket));
    }
}
