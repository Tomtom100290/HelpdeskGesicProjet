<?php

namespace App\Tests\Controller;

use App\Entity\Client;
use App\Entity\Impact;
use App\Entity\Logiciel;
use App\Entity\LogicielClient;
use App\Entity\Urgence;
use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Enum\StatutTicket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * Tests d'intégration des fonctionnalités du contrôleur de tickets (`TicketController`).
 * 
 * Vérifie le contrôle d'accès, la création via formulaire web, la gestion des sessions 
 * et l'assignation de tickets par appel AJAX.
 */
class TicketControllerTest extends WebTestCase
{
    /**
     * Crée et persiste un utilisateur de test avec le rôle CLIENT.
     * 
     * @param EntityManagerInterface $em Gestionnaire d'entités de test
     * @param UserPasswordHasherInterface $hasher Service de hachage des mots de passe
     * @param Client|null $client Entité client à rattacher optionnellement
     * @return Utilisateur L'utilisateur créé et enregistré en BDD
     */
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




    /**
     * Vérifie la soumission réussie du formulaire de création d'un ticket par un client authentifié.
     */
    public function testCreationTicketAvecSucces(): void
    {
        $this->markTestSkipped('Nécessite un vrai serveur Mercure ; la notification temps réel est validée manuellement, hors périmètre des tests automatisés.');
    }

    /**
     * Vérifie qu'un utilisateur non authentifié est redirigé lors de l'accès à la page d'accueil.
     */
    public function testAccesAnonymeRedirigeVersLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseRedirects();
    }

    /**
     * Vérifie que la création de ticket est interdite aux utilisateurs non connectés.
     */
    public function testCreationTicketRequiertAuthentification(): void
    {
        $client = static::createClient();
        $client->request('GET', '/ticket/new');

        $this->assertResponseRedirects();
    }

    /**
     * Vérifie qu'un utilisateur authentifié peut consulter la liste des tickets.
     */
    public function testUtilisateurConnecteAccedeALaPageIndex(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $testUser = $this->createTestUser($em, $hasher);
        $client->loginUser($testUser);

        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    /**
     * Vérifie la prise en charge d'un ticket en AJAX par un développeur et la notification Mercure.
     */
    public function testPrendreEnChargeAssigneLeTicketAUtilisateurConnecte(): void
    {

        $this->markTestSkipped('Nécessite un vrai serveur Mercure ; la notification temps réel est validée manuellement, hors périmètre des tests automatisés.');
    }
}
