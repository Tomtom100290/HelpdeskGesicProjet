<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Enum\Role;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    private function createMessage(): Message
    {
        $message = new Message();
        $message->setContenu('Bonjour, avez-vous plus de détails ?');

        return $message;
    }

    public function testGetterSetterContenu(): void
    {
        $message = $this->createMessage();

        $this->assertSame('Bonjour, avez-vous plus de détails ?', $message->getContenu());
    }

    public function testGetterSetterTicket(): void
    {
        $message = $this->createMessage();
        $ticket = new Ticket();

        $message->setTicket($ticket);

        $this->assertSame($ticket, $message->getTicket());
    }

    public function testGetterSetterUtilisateur(): void
    {
        $message = $this->createMessage();
        $utilisateur = new Utilisateur();
        $utilisateur->setRole(Role::CLIENT);

        $message->setUtilisateur($utilisateur);

        $this->assertSame($utilisateur, $message->getUtilisateur());
    }

    public function testMessageParentEstNullParDefaut(): void
    {
        $message = $this->createMessage();

        $this->assertNull($message->getMessageParent());
    }

    public function testGetterSetterMessageParent(): void
    {
        $message = $this->createMessage();
        $parent = $this->createMessage();

        $message->setMessageParent($parent);

        $this->assertSame($parent, $message->getMessageParent());
    }

    public function testGetReponsesRetourneUneCollectionVideParDefaut(): void
    {
        $message = $this->createMessage();

        $this->assertCount(0, $message->getReponses());
    }

    public function testMessageEstActifParDefaut(): void
    {
        $message = $this->createMessage();

        $this->assertTrue($message->isTopActif());
    }

    public function testGetterSetterTopActif(): void
    {
        $message = $this->createMessage();
        $message->setTopActif(false);

        $this->assertFalse($message->isTopActif());
    }

    public function testDateEnvoiEstInitialiseeAutomatiquement(): void
    {
        $message = $this->createMessage();

        $this->assertInstanceOf(\DateTimeImmutable::class, $message->getDateEnvoi());
    }
}
