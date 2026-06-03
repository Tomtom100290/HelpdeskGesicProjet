<?php

namespace App\Enum;

enum StatutTache: string
{
    case A_FAIRE   = 'a_faire';
    case EN_COURS  = 'en_cours';
    case REALISEE  = 'realisee';

    public function label(): string
    {
        return match($this) {
            StatutTache::A_FAIRE  => 'À faire',
            StatutTache::EN_COURS => 'En cours',
            StatutTache::REALISEE => 'Réalisée',
        };
    }
}
