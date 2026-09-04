<?php

namespace App\Tests\Form;

use App\Entity\Impact;
use App\Entity\Logiciel;
use App\Entity\LogicielClient;
use App\Entity\Ticket;
use App\Entity\Urgence;
use App\Entity\Utilisateur;
use App\Entity\Client;
use App\Form\NvxTicketType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NvxTicketTypeTest extends KernelTestCase
{
    public function testSubmitValidData(): void
    {
        self::bootKernel();
        $formFactory = static::getContainer()->get('form.factory');

        // 1. Instanciation des dépendances d'entités avec leurs données minimales
        $impact = new Impact();
        $urgence = new Urgence();
        $destinataire = new Utilisateur();

        $client = new Client();
        $logiciel = new Logiciel();

        $logicielClient = new LogicielClient();
        $logicielClient->setClient($client);
        $logicielClient->setLogiciel($logiciel);

        // 2. Préparation du jeu de données de test
        $formData = [
            'titre' => 'Problème de connexion BDD',
            'description' => 'Impossible de se connecter au serveur distant.',
            'impact' => $impact,
            'urgence' => $urgence,
            'destinataire' => $destinataire,
            'logicielClient' => $logicielClient,
        ];

        $modelToCompare = new Ticket();

        // 3. Création du formulaire avec l'option obligatoire 'id_client'
        $form = $formFactory->create(NvxTicketType::class, $modelToCompare, [
            'id_client' => 1,
        ]);

        // 4. Soumission
        $form->submit($formData);

        // 5. Assertions
        $this->assertTrue($form->isSynchronized(), 'Le formulaire NvxTicketType doit se synchroniser sans erreur.');
        $this->assertEquals('Problème de connexion BDD', $modelToCompare->getTitre());
        $this->assertEquals('Impossible de se connecter au serveur distant.', $modelToCompare->getDescription());
    }
}
