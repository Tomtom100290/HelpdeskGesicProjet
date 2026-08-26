<?php

namespace App\Service;

use App\Entity\Ticket;

/**
 * Calcule la priorité d'un ticket à partir de son impact et de son urgence.
 * Extrait du contrôleur pour permettre un test unitaire simple, sans HTTP ni base de données.
 */
class PriorityCalculator
{
    public function calculate(Ticket $ticket): int
    {
        try {
            $impact = $ticket->getImpact();
            $urgence = $ticket->getUrgence();
        } catch (\Error $e) {
            // Les propriétés impact/urgence de Ticket sont typées non-nullables :
            // y accéder avant initialisation lève une \Error native, qu'on transforme
            // ici en exception métier plus explicite et plus facile à tester.
            throw new \LogicException('Impact et urgence doivent être définis pour calculer la priorité.', 0, $e);
        }

        return $impact->getNote() * $urgence->getNote();
    }
}
