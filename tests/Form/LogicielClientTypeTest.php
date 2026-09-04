<?php

namespace App\Tests\Form;

use App\Entity\Client;
use App\Entity\Logiciel;
use App\Entity\LogicielClient;
use App\Form\LogicielClientType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogicielClientTypeTest extends KernelTestCase
{
    public function testSubmitValidData(): void
    {
        self::bootKernel();
        $formFactory = static::getContainer()->get('form.factory');

        // 1. Instanciation des objets associés
        $client = new Client();
        $logiciel = new Logiciel();

        // 2. Préparation du jeu de données
        $formData = [
            'dateInstallation' => '2026-09-04',
            'versionLogiciel'  => '2.1.0',
            'notes'            => 'Installation initiale sur le serveur client.',
            'client'           => $client,
            'logiciel'         => $logiciel,
        ];

        $modelToCompare = new LogicielClient();
        $form = $formFactory->create(LogicielClientType::class, $modelToCompare);

        // 3. Soumission des données
        $form->submit($formData);

        // 4. Assertions
        $this->assertTrue($form->isSynchronized(), 'Le formulaire LogicielClientType doit se synchroniser sans erreur.');
        $this->assertEquals('2.1.0', $modelToCompare->getVersionLogiciel());
        $this->assertEquals('Installation initiale sur le serveur client.', $modelToCompare->getNotes());
    }
}
