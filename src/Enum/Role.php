<?php

namespace App\Enum;

enum Role: string
{
    case CLIENT      = 'client';
    case DEVELOPPEUR = 'developpeur';
    case ADMIN       = 'admin';

    public function label(): string
    {
        return match($this) {
            Role::CLIENT      => 'Client',
            Role::DEVELOPPEUR => 'Développeur',
            Role::ADMIN       => 'Administrateur',
        };
    }
}
