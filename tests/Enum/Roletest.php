<?php

namespace App\Tests\Enum;

use App\Enum\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testValeursDesRoles(): void
    {
        $this->assertSame('ROLE_CLIENT', Role::CLIENT->value);
        $this->assertSame('ROLE_DEVELOPPEUR', Role::DEVELOPPEUR->value);
        $this->assertSame('ROLE_ADMIN', Role::ADMIN->value);
    }

    public function testLabelsDesRoles(): void
    {
        $this->assertSame('Client', Role::CLIENT->label());
        $this->assertSame('Développeur', Role::DEVELOPPEUR->label());
        $this->assertSame('Administrateur', Role::ADMIN->label());
    }

    public function testToutesLesValeursOntUnLabel(): void
    {
        foreach (Role::cases() as $role) {
            $this->assertNotEmpty($role->label());
        }
    }
}
