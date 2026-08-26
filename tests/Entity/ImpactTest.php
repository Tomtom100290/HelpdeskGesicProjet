<?php

namespace App\Tests\Entity;

use App\Entity\Impact;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ImpactTest extends TestCase
{
    public function testGettersSetters(): void
    {
        $impact = new Impact();
        $impact->setNiveau('I3');
        $impact->setLibelle('Impact élevé');
        $impact->setPrompt('Ce problème impacte fortement mon activité');
        $impact->setNote(3);

        $this->assertSame('I3', $impact->getNiveau());
        $this->assertSame('Impact élevé', $impact->getLibelle());
        $this->assertSame('Ce problème impacte fortement mon activité', $impact->getPrompt());
        $this->assertSame(3, $impact->getNote());
    }

    public function testToStringFormatteNiveauEtLibelle(): void
    {
        $impact = new Impact();
        $impact->setNiveau('I1');
        $impact->setLibelle('Impact faible');

        $this->assertSame('I1 — Impact faible', (string) $impact);
    }

    #[DataProvider('notesInvalides')]
    public function testSetNoteRejetteLesValeursHorsBornes(int $noteInvalide): void
    {
        $impact = new Impact();

        $this->expectException(\InvalidArgumentException::class);
        $impact->setNote($noteInvalide);
    }

    public static function notesInvalides(): array
    {
        return [
            'note trop basse' => [0],
            'note négative'   => [-1],
            'note trop haute' => [5],
        ];
    }

    #[DataProvider('notesValides')]
    public function testSetNoteAccepteLesValeursValides(int $noteValide): void
    {
        $impact = new Impact();
        $impact->setNote($noteValide);

        $this->assertSame($noteValide, $impact->getNote());
    }

    public static function notesValides(): array
    {
        return [
            'note minimale' => [1],
            'note maximale' => [4],
        ];
    }
}
