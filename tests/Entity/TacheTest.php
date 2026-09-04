<?php

namespace App\Tests\Entity;

use App\Entity\Tache;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Enum\StatutTache;
use PHPUnit\Framework\TestCase;

class TacheTest extends TestCase
{
    private function createUtilisateur(): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setRole(Role::DEVELOPPEUR);

        return $utilisateur;
    }

    private function createTache(): Tache
    {
        $tache = new Tache();
        $tache->setLibelle('Vérifier la configuration serveur');
        $tache->setUtilisateurCreateur($this->createUtilisateur());

        return $tache;
    }

    public function testGetterSetterLibelle(): void
    {
        $tache = $this->createTache();

        $this->assertSame('Vérifier la configuration serveur', $tache->getLibelle());
    }

    public function testGetterSetterDescription(): void
    {
        $tache = $this->createTache();
        $tache->setDescription('Description détaillée de la tâche');

        $this->assertSame('Description détaillée de la tâche', $tache->getDescription());
    }

    public function testDescriptionEstNullableParDefaut(): void
    {
        $tache = $this->createTache();

        $this->assertNull($tache->getDescription());
    }

    public function testStatutParDefautEstAFaire(): void
    {
        $tache = $this->createTache();

        $this->assertSame(StatutTache::A_FAIRE, $tache->getStatut());
    }

    public function testGetterSetterStatut(): void
    {
        $tache = $this->createTache();
        $tache->setStatut(StatutTache::EN_COURS);

        $this->assertSame(StatutTache::EN_COURS, $tache->getStatut());
    }

    public function testGetterSetterTicket(): void
    {
        $tache = $this->createTache();
        $ticket = new Ticket();

        $tache->setTicket($ticket);

        $this->assertSame($ticket, $tache->getTicket());
    }

    public function testTicketEstNullableParDefaut(): void
    {
        $tache = $this->createTache();

        $this->assertNull($tache->getTicket());
    }

    public function testGetterSetterUtilisateurAssigne(): void
    {
        $tache = $this->createTache();
        $developpeur = $this->createUtilisateur();

        $tache->setUtilisateurAssigne($developpeur);

        $this->assertSame($developpeur, $tache->getUtilisateurAssigne());
    }

    public function testGetterSetterUtilisateurCreateur(): void
    {
        $utilisateur = $this->createUtilisateur();
        $tache = new Tache();
        $tache->setLibelle('Tâche test');
        $tache->setUtilisateurCreateur($utilisateur);

        $this->assertSame($utilisateur, $tache->getUtilisateurCreateur());
    }

    public function testGetterSetterDateRealisation(): void
    {
        $tache = $this->createTache();
        $date = new \DateTime('2026-09-01');

        $tache->setDateRealisation($date);

        $this->assertSame($date, $tache->getDateRealisation());
    }

    public function testDateCreationEstInitialiseeAutomatiquement(): void
    {
        $tache = $this->createTache();

        $this->assertInstanceOf(\DateTimeImmutable::class, $tache->getDateCreation());
    }
}
