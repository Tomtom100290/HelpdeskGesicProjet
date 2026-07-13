<?php

namespace App\Enum;

enum StatutTicket: string
{
    case NOUVEAU        = 'nouveau';
    case EN_COURS       = 'en_cours';
    case EN_ATTENTE     = 'en_attente';
    case RESOLU         = 'résolu';
    case FERMER         = 'fermé';
    case REJETER        = 'rejeté';

    public function label(): string
    {
        return match ($this) {
            StatutTicket::NOUVEAU    => 'Nouveau',
            StatutTicket::EN_COURS   => 'En cours',
            StatutTicket::EN_ATTENTE => 'En attente',
            StatutTicket::RESOLU     => 'Résolu',
            StatutTicket::FERMER     => 'Fermé',
            StatutTicket::REJETER    => 'Rejeté',
        };
    }
    public function color(): string
    {
        return match ($this) {
            StatutTicket::NOUVEAU    => 'bg-yellow-50 text-yellow-600',
            StatutTicket::EN_COURS   => 'bg-purple-50 text-purple-600',
            StatutTicket::EN_ATTENTE => 'bg-amber-50 text-amber-600',
            StatutTicket::RESOLU     => 'bg-green-50 text-green-600',
            StatutTicket::FERMER     => 'bg-gray-100 text-gray-600',
            StatutTicket::REJETER    => 'bg-red-50 text-red-600',
        };
    }
}
