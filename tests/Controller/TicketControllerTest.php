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
     * Instancie les entités de référence (Client, Impact, Urgence, Logiciel) nécessaires aux formulaires.
     * 
     * @param EntityManagerInterface $em Gestionnaire d'entités de test
     * @return array<string, object> Tableau associatif contenant les entités créées
     */
    private function createDonneesReference(EntityManagerInterface $em): array
    {
        $uniq = uniqid();

        $client = new Client();
        $client->setRaisonSocial('Client de test ' . $uniq);
        $em->persist($client);

        $impact = new Impact();
        $impact->setNiveau('I' . rand(100, 999));
        $impact->setLibelle('Impact modéré');
        $impact->setPrompt('Impact modéré sur mon activité');
        $impact->setNote(2);
        $em->persist($impact);

        $urgence = new Urgence();
        $urgence->setNiveau('U' . rand(100, 999));
        $urgence->setLibelle('Urgence élevée');
        $urgence->setPrompt('Ce problème est urgent');
        $urgence->setNote(3);
        $em->persist($urgence);

        $logiciel = new Logiciel();
        $logiciel->setLibelle('Logiciel ' . $uniq);
        $em->persist($logiciel);

        $logicielClient = new LogicielClient();
        $logicielClient->setClient($client);
        $logicielClient->setLogiciel($logiciel);
        $em->persist($logicielClient);

        $em->flush();

        return compact('client', 'impact', 'urgence', 'logiciel', 'logicielClient');
    }

    /**
     * Vérifie la soumission réussie du formulaire de création d'un ticket par un client authentifié.
     */
    public function testCreationTicketAvecSucces(): void
    {
        $this->markTestSkipped('Nécessite un vrai serveur Mercure ; la notification temps réel est validée manuellement, hors périmètre des tests automatisés.');
        $client = static::createClient();


        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        // 1. Initialisation des données de référence (client, impacts, logiciels)
        $donnees = $this->createDonneesReference($em);

        // 2. Création et authentification d'un utilisateur de test
        $testUser = $this->createTestUser($em, $hasher, $donnees['client']);
        $client->loginUser($testUser);

        // 3. Affichage du formulaire de création
        $crawler = $client->request('GET', '/ticket/new');
        $this->assertResponseIsSuccessful();

        // 4. Remplissage et soumission du formulaire web
        $form = $crawler->filter('form')->form([
            'nvx_ticket[titre]' => 'Problème d\'impression',
            'nvx_ticket[description]' => 'Impossible d\'imprimer depuis ce matin.',
            'nvx_ticket[impact]' => $donnees['impact']->getId(),
            'nvx_ticket[urgence]' => $donnees['urgence']->getId(),
            'nvx_ticket[logicielClient]' => $donnees['logicielClient']->getId(),
        ]);

        $client->submit($form);

        // 5. Validation de la redirection post-soumission et suivi du lien
        $this->assertResponseRedirects('/');
        $client->followRedirect();

        // 6. Assertion : Présence du titre du nouveau ticket sur la page d'accueil
        $this->assertSelectorTextContains('html', 'Problème d\'impression');
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

        // 2. Initialisation du navigateur de test
        $client = static::createClient();




        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $donnees = $this->createDonneesReference($em);
        $createur = $this->createTestUser($em, $hasher, $donnees['client']);

        // 4. Création d'un profil Développeur en BDD
        $developpeur = new Utilisateur();
        $developpeur->setNom('Dev');
        $developpeur->setPrenom('Test');
        $developpeur->setEmail('dev_' . uniqid() . '@test.com');
        $developpeur->setRole(Role::DEVELOPPEUR);
        $developpeur->setMotDePasse($hasher->hashPassword($developpeur, 'password_test_123'));
        $em->persist($developpeur);

        // 5. Création d'un ticket non assigné
        $ticket = new \App\Entity\Ticket();
        $ticket->setTitre('Panne réseau');
        $ticket->setDescription('Plus de connexion depuis ce matin');
        $ticket->setCreateur($createur);
        $ticket->setImpact($donnees['impact']);
        $ticket->setUrgence($donnees['urgence']);
        $ticket->setLogicielClient($donnees['logicielClient']);
        $ticket->setPrioriteCalculee(6);
        $em->persist($ticket);
        $em->flush();

        // 6. Exécution de la requête AJAX de prise en charge par le développeur
        $client->loginUser($developpeur);
        $client->request(
            'POST',
            '/ticket/' . $ticket->getId() . '/prendre-en-charge',
            server: ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        // 7. Assertions sur la réponse JSON
        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);

        // 8. Vérification des mutations en base de données (statut et développeur assigné)
        $em->refresh($ticket);
        $this->assertSame(StatutTicket::EN_COURS, $ticket->getStatut());
        $this->assertSame($developpeur->getId(), $ticket->getAssigne()?->getId());
    }
}
