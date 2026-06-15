<?php

namespace App\Enum;

enum StatutTicket: string
{
    case NOUVEAU        = 'a_faire';
    case EN_COURS       = 'en_cours';
    case EN_ATTENTE     = 'en_attente';
    case RESOLU         = 'résolu';
    case FERMER         = 'fermé';
    case REJETER        = 'rejeté';

    public function label(): string
    {
        return match ($this) {
            StatutTicket::NOUVEAU        => 'Nouveau',
            StatutTicket::EN_COURS       => 'En cours',
            StatutTicket::EN_ATTENTE     => 'En attente',
            StatutTicket::RESOLU         => 'Résolu',
            StatutTicket::FERMER         => 'Fermé',
            StatutTicket::REJETER        => 'Rejeté',
        };
    }
}
