<?php

namespace App\Tests\Entity;

use App\Entity\Logiciel;
use App\Entity\LogicielClient;
use PHPUnit\Framework\TestCase;

class LogicielTest extends TestCase
{
    private function createLogiciel(): Logiciel
    {
        $logiciel = new Logiciel();
        $logiciel->setLibelle('Progiplus');

        return $logiciel;
    }

    public function testGetterSetterLibelle(): void
    {
        $logiciel = $this->createLogiciel();

        $this->assertSame('Progiplus', $logiciel->getLibelle());
    }

    public function testGetterSetterDescription(): void
    {
        $logiciel = $this->createLogiciel();
        $logiciel->setDescription('Logiciel de gestion commerciale');

        $this->assertSame('Logiciel de gestion commerciale', $logiciel->getDescription());
    }

    public function testDescriptionEstNullableParDefaut(): void
    {
        $logiciel = $this->createLogiciel();

        $this->assertNull($logiciel->getDescription());
    }

    public function testGetterSetterTypeLogiciel(): void
    {
        $logiciel = $this->createLogiciel();
        $logiciel->setTypeLogiciel('ERP');

        $this->assertSame('ERP', $logiciel->getTypeLogiciel());
    }

    public function testGetterSetterCoeffCriticite(): void
    {
        $logiciel = $this->createLogiciel();
        $logiciel->setCoeffCriticite(2.5);

        $this->assertSame(2.5, $logiciel->getCoeffCriticite());
    }

    public function testCoeffCriticiteValeurParDefaut(): void
    {
        $logiciel = $this->createLogiciel();

        $this->assertSame(1.0, $logiciel->getCoeffCriticite());
    }

    public function testLogicielEstActifParDefaut(): void
    {
        $logiciel = $this->createLogiciel();

        $this->assertTrue($logiciel->isTopActif());
    }

    public function testGetterSetterTopActif(): void
    {
        $logiciel = $this->createLogiciel();
        $logiciel->setTopActif(false);

        $this->assertFalse($logiciel->isTopActif());
    }

    public function testGetLogicielsClientRetourneUneCollectionVideParDefaut(): void
    {
        $logiciel = $this->createLogiciel();

        $this->assertCount(0, $logiciel->getLogicielsClient());
    }

    public function testAjoutDeLogicielClientDansLaCollection(): void
    {
        $logiciel = $this->createLogiciel();
        $logicielClient = new LogicielClient();

        $logiciel->getLogicielsClient()->add($logicielClient);

        $this->assertCount(1, $logiciel->getLogicielsClient());
        $this->assertTrue($logiciel->getLogicielsClient()->contains($logicielClient));
    }
}
