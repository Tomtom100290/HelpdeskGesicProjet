<?php

namespace App\Tests\Controller;

use App\Entity\Client;
use App\Entity\Utilisateur;
use App\Enum\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TicketControllerTest extends WebTestCase
{
    private function createTestUser(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        ?Client $client = null
    ): Utilisateur {
        $uniq = uniqid();
        $utilisateur = new Utilisateur();
        $utilisateur->setNom('Test');
        $utilisateur->setPrenom('Client');
        $utilisateur->setEmail('client_' . $uniq . '@test.com');
        $utilisateur->setRole(Role::CLIENT);
        $utilisateur->setMotDePasse($hasher->hashPassword($utilisateur, 'password_test_123'));

        if ($client !== null) {
            $utilisateur->setClient($client);
        }

        $em->persist($utilisateur);
        $em->flush();

        return $utilisateur;
    }

    public function testAccesAnonymeRedirigeVersLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects();
    }

    public function testCreationTicketRequiertAuthentification(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ticket/new');

        $this->assertResponseRedirects();
    }

    public function testUtilisateurConnecteAccedeALaPageIndex(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $testUser = $this->createTestUser($em, $hasher);
        $client->loginUser($testUser);

        // Mock du Hub Mercure pour éviter d'avoir besoin du serveur Mercure actif
        $mercureHubMock = $this->createMock(HubInterface::class);
        $client->getContainer()->set(HubInterface::class, $mercureHubMock);

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }
}
