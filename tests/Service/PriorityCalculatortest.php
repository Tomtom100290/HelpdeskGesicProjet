<?php

namespace App\Tests\Service;

use App\Entity\Impact;
use App\Entity\Ticket;
use App\Entity\Urgence;
use App\Service\PriorityCalculator;
use PHPUnit\Framework\TestCase;

class PriorityCalculatorTest extends TestCase
{
    private PriorityCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PriorityCalculator();
    }

    public function testCalculMultiplieImpactParUrgence(): void
    {
        $impact = new Impact();
        $impact->setNote(3);

        $urgence = new Urgence();
        $urgence->setNote(4);

        $ticket = new Ticket();
        $ticket->setImpact($impact);
        $ticket->setUrgence($urgence);

        $this->assertSame(12, $this->calculator->calculate($ticket));
    }

    public function testCalculAvecNotesMinimales(): void
    {
        $impact = new Impact();
        $impact->setNote(1);

        $urgence = new Urgence();
        $urgence->setNote(1);

        $ticket = new Ticket();
        $ticket->setImpact($impact);
        $ticket->setUrgence($urgence);

        $this->assertSame(1, $this->calculator->calculate($ticket));
    }

    public function testLeveExceptionSiImpactOuUrgenceManquant(): void
    {
        // Un ticket fraîchement construit n'a ni impact ni urgence définis
        $ticket = new Ticket();

        $this->expectException(\LogicException::class);
        $this->calculator->calculate($ticket);
    }
}
