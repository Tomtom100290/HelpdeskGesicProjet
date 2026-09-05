<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Client;
use App\Entity\Logiciel;
use App\Entity\LogicielClient;
use App\Entity\Utilisateur;
use App\Enum\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class LogicielClientControllerTest extends WebTestCase
{
    /**
     * Crée un utilisateur de test avec le rôle spécifié.
     */
    private function createTestUser(EntityManagerInterface $em, KernelBrowser $client, Role $role): Utilisateur
    {
        $hasher = $client->getContainer()->get(UserPasswordHasherInterface::class);
        $uniq = uniqid();

        $user = new Utilisateur();
        $user->setNom('Test');
        $user->setPrenom('User');
        $user->setEmail('user_' . $uniq . '@test.com');
        $user->setRole($role);
        $user->setMotDePasse($hasher->hashPassword($user, 'password123'));

        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Crée les entités de référence (Client, Logiciel).
     */
    private function createFixtures(EntityManagerInterface $em): array
    {
        $uniq = uniqid();

        $client = new Client();
        $client->setRaisonSocial('Entreprise ' . $uniq);
        $em->persist($client);

        $logiciel = new Logiciel();
        $logiciel->setLibelle('GesicPro ' . $uniq);
        $em->persist($logiciel);

        $em->flush();

        return [$client, $logiciel];
    }

    public function testAccessDeniedForNonAuthorizedUsers(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get(EntityManagerInterface::class);

        // 1. Accès anonyme -> Redirection
        $client->request('GET', '/logicielclient');
        $this->assertResponseRedirects();

        // 2. Accès avec ROLE_CLIENT -> 403 Forbidden
        $clientUser = $this->createTestUser($em, $client, Role::CLIENT);
        $client->loginUser($clientUser);

        $client->request('GET', '/logicielclient');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testIndex(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get(EntityManagerInterface::class);

        $devUser = $this->createTestUser($em, $client, Role::DEVELOPPEUR);
        $client->loginUser($devUser);

        [$clientEntity, $logicielEntity] = $this->createFixtures($em);

        $lc = new LogicielClient();
        $lc->setClient($clientEntity);
        $lc->setLogiciel($logicielEntity);
        $lc->setVersionLogiciel('v2.1.0');
        $em->persist($lc);
        $em->flush();

        $client->request('GET', '/logicielclient');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', $clientEntity->getRaisonSocial());
    }

    public function testNew(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get(EntityManagerInterface::class);

        $adminUser = $this->createTestUser($em, $client, Role::ADMIN);
        $client->loginUser($adminUser);

        [$clientEntity, $logicielEntity] = $this->createFixtures($em);

        $crawler = $client->request('GET', '/logicielclient/new');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form([
            'logiciel_client[client]' => $clientEntity->getId(),
            'logiciel_client[logiciel]' => $logicielEntity->getId(),
            'logiciel_client[versionLogiciel]' => 'v1.0.0',
            'logiciel_client[notes]' => 'Installation initiale.',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/logicielclient');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testShow(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get(EntityManagerInterface::class);

        $devUser = $this->createTestUser($em, $client, Role::DEVELOPPEUR);
        $client->loginUser($devUser);

        [$clientEntity, $logicielEntity] = $this->createFixtures($em);

        $lc = new LogicielClient();
        $lc->setClient($clientEntity);
        $lc->setLogiciel($logicielEntity);
        $lc->setVersionLogiciel('v3.0');
        $em->persist($lc);
        $em->flush();

        $client->request('GET', '/logicielclient/' . $lc->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'v3.0');
    }

    public function testEdit(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get(EntityManagerInterface::class);

        $devUser = $this->createTestUser($em, $client, Role::DEVELOPPEUR);
        $client->loginUser($devUser);

        [$clientEntity, $logicielEntity] = $this->createFixtures($em);

        $lc = new LogicielClient();
        $lc->setClient($clientEntity);
        $lc->setLogiciel($logicielEntity);
        $lc->setVersionLogiciel('v1.0');
        $em->persist($lc);
        $em->flush();

        $crawler = $client->request('GET', '/logicielclient/' . $lc->getId() . '/edit');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Mettre à jour')->form([
            'logiciel_client[versionLogiciel]' => 'v1.1-MAJ',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/logicielclient');

        $em->refresh($lc);
        $this->assertEquals('v1.1-MAJ', $lc->getVersionLogiciel());
    }

    public function testDelete(): void
    {
        $client = static::createClient();
        $em = $client->getContainer()->get(EntityManagerInterface::class);

        $adminUser = $this->createTestUser($em, $client, Role::ADMIN);
        $client->loginUser($adminUser);

        [$clientEntity, $logicielEntity] = $this->createFixtures($em);

        $lc = new LogicielClient();
        $lc->setClient($clientEntity);
        $lc->setLogiciel($logicielEntity);
        $em->persist($lc);
        $em->flush();

        $id = $lc->getId();

        $crawler = $client->request('GET', '/logicielclient/' . $id);
        $token = $crawler->filter('input[name="_token"]')->attr('value');

        $client->request('POST', '/logicielclient/' . $id, [
            '_token' => $token,
        ]);

        $this->assertResponseRedirects('/logicielclient');

        $deletedLc = $em->getRepository(LogicielClient::class)->find($id);
        $this->assertNull($deletedLc);
    }
}
