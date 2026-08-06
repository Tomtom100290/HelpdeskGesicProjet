<?php

namespace App\Tests\Entity;

use App\Entity\Ticket;
use App\Entity\Impact;
use App\Entity\Urgence;
use PHPUnit\Framework\TestCase;

class TicketTest extends TestCase
{
    public function testCalculPriorite(): void
    {
        $ticket = new Ticket();

        $impact = new Impact();
        $impact->setNote(3);

        $urgence = new Urgence();
        $urgence->setNote(2);

        $ticket->setImpact($impact);
        $ticket->setUrgence($urgence);

        // Simulation du calcul : Impact * Urgence
        $score = $ticket->getImpact()->getNote() * $ticket->getUrgence()->getNote();
        $ticket->setPrioriteCalculee($score);

        // Assertion PHPUnit : on vérifie que 3 * 2 fait bien 6
        $this->assertEquals(6, $ticket->getPrioriteCalculee());
    }
}
