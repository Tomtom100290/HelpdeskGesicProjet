<?php

namespace App\Tests\Entity;

use App\Entity\HistoriqueStatutTicket;
use App\Entity\StatutTicket;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Enum\Role;
use PHPUnit\Framework\TestCase;

class HistoriqueStatutTicketTest extends TestCase
{
    private function createHistorique(): HistoriqueStatutTicket
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setRole(Role::ADMIN);

        $historique = new HistoriqueStatutTicket();
        $historique->setTicket(new Ticket());
        $historique->setStatutAvant(new StatutTicket());
        $historique->setStatutApres(new StatutTicket());
        $historique->setUtilisateur($utilisateur);

        return $historique;
    }

    public function testGetterSetterTicket(): void
    {
        $historique = $this->createHistorique();
        $ticket = new Ticket();

        $historique->setTicket($ticket);

        $this->assertSame($ticket, $historique->getTicket());
    }

    public function testGetterSetterStatutAvant(): void
    {
        $historique = $this->createHistorique();
        $statutAvant = new StatutTicket();

        $historique->setStatutAvant($statutAvant);

        $this->assertSame($statutAvant, $historique->getStatutAvant());
    }

    public function testGetterSetterStatutApres(): void
    {
        $historique = $this->createHistorique();
        $statutApres = new StatutTicket();

        $historique->setStatutApres($statutApres);

        $this->assertSame($statutApres, $historique->getStatutApres());
    }

    public function testGetterSetterUtilisateur(): void
    {
        $historique = $this->createHistorique();
        $utilisateur = new Utilisateur();
        $utilisateur->setRole(Role::DEVELOPPEUR);

        $historique->setUtilisateur($utilisateur);

        $this->assertSame($utilisateur, $historique->getUtilisateur());
    }

    public function testGetterSetterCommentaire(): void
    {
        $historique = $this->createHistorique();
        $historique->setCommentaire('Changement suite à intervention');

        $this->assertSame('Changement suite à intervention', $historique->getCommentaire());
    }

    public function testCommentaireEstNullableParDefaut(): void
    {
        $historique = $this->createHistorique();

        $this->assertNull($historique->getCommentaire());
    }

    public function testDateChangementEstInitialiseeAutomatiquement(): void
    {
        $historique = $this->createHistorique();

        $this->assertInstanceOf(\DateTimeImmutable::class, $historique->getDateChangement());
    }
}
