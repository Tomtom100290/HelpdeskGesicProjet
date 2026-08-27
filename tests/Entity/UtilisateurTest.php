<?php

namespace App\Tests\Entity;

use App\Entity\Client;
use App\Entity\Utilisateur;
use App\Enum\Role;
use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{
    private function createUtilisateur(Role $role = Role::CLIENT): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Dupont');
        $utilisateur->setPrenom('Jean');
        $utilisateur->setEmail('jean.dupont@example.com');
        $utilisateur->setRole($role);
        $utilisateur->setMotDePasse('hash_bidon');

        return $utilisateur;
    }

    public function testGetUserIdentifierRetourneEmail(): void
    {
        $utilisateur = $this->createUtilisateur();

        $this->assertSame('jean.dupont@example.com', $utilisateur->getUserIdentifier());
    }

    public function testGetRolesRetourneLeRoleSousFormeDeTableau(): void
    {
        $utilisateur = $this->createUtilisateur(Role::ADMIN);

        $this->assertSame(['ROLE_ADMIN', 'ROLE_USER'], $utilisateur->getRoles());
    }

    public function testGetPasswordRetourneLeMotDePasseHache(): void
    {
        $utilisateur = $this->createUtilisateur();

        $this->assertSame('hash_bidon', $utilisateur->getPassword());
    }

    public function testUtilisateurEstActifParDefaut(): void
    {
        $utilisateur = $this->createUtilisateur();

        $this->assertTrue($utilisateur->isTopActif());
    }

    public function testAssociationAvecUnClient(): void
    {
        $utilisateur = $this->createUtilisateur();
        $client = new Client();
        $client->setRaisonSocial('Entreprise Test');

        $utilisateur->setClient($client);

        $this->assertSame($client, $utilisateur->getClient());
    }

    public function testDateCreationEstInitialiseeAutomatiquement(): void
    {
        $utilisateur = new Utilisateur();

        $this->assertInstanceOf(\DateTimeImmutable::class, $utilisateur->getDateCreation());
    }

    public function testGetterSetterNumTel(): void
    {
        $utilisateur = $this->createUtilisateur();
        $utilisateur->setNumTel('0601020304');

        $this->assertSame('0601020304', $utilisateur->getNumTel());
    }
}
