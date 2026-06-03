<?php

namespace App\Enum;

enum NiveauImpact: string
{
    case ISOLE      = 'isole';
    case EQUIPE     = 'equipe';
    case ENTREPRISE = 'entreprise';

    public function label(): string
    {
        return match($this) {
            NiveauImpact::ISOLE      => 'Moi uniquement',
            NiveauImpact::EQUIPE     => 'Mon équipe / département',
            NiveauImpact::ENTREPRISE => 'Toute l\'entreprise',
        };
    }

    public function points(): int
    {
        return match($this) {
            NiveauImpact::ISOLE      => 1,
            NiveauImpact::EQUIPE     => 3,
            NiveauImpact::ENTREPRISE => 5,
        };
    }
}
