<?php

namespace App\Tests\Entity;

use App\Entity\Urgence;
use PHPUnit\Framework\TestCase;

class UrgenceTest extends TestCase
{
    public function testGettersSetters(): void
    {
        $urgence = new Urgence();
        $urgence->setNiveau('U2');
        $urgence->setLibelle('Urgence modérée');
        $urgence->setPrompt('Ce problème doit être traité rapidement');
        $urgence->setNote(2);

        $this->assertSame('U2', $urgence->getNiveau());
        $this->assertSame('Urgence modérée', $urgence->getLibelle());
        $this->assertSame('Ce problème doit être traité rapidement', $urgence->getPrompt());
        $this->assertSame(2, $urgence->getNote());
    }

    public function testToStringFormatteNiveauEtLibelle(): void
    {
        $urgence = new Urgence();
        $urgence->setNiveau('U4');
        $urgence->setLibelle('Urgence critique');

        $this->assertSame('U4 — Urgence critique', (string) $urgence);
    }
}
