<?php

namespace App\Enum;

enum Role: string
{
    case CLIENT      = 'ROLE_CLIENT';
    case DEVELOPPEUR = 'ROLE_DEVELOPPEUR';
    case ADMIN       = 'ROLE_ADMIN';

    public function label(): string
    {
        return match($this) {
            Role::CLIENT      => 'Client',
            Role::DEVELOPPEUR => 'Développeur',
            Role::ADMIN       => 'Administrateur',
        };
    }
}