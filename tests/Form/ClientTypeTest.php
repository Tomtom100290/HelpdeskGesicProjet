<?php

namespace App\Tests\Form;

use App\Entity\Client;
use App\Form\ClientType;
use Symfony\Component\Form\Test\TypeTestCase;

class ClientTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        // 1. Données brutes soumises au formulaire
        $formData = [
            'raisonSocial' => 'Entreprise Test GESIC',
            'email'        => 'contact@testgesic.fr',
            'numTel'       => '0102030405',
            'adresse'      => '123 Rue du Code',
            'codePostal'   => '75001',
            'ville'        => 'Paris',
            'topActif'     => true,
        ];

        // 2. Instanciation du modèle et du formulaire
        $modelToCompare = new Client();
        $form = $this->factory->create(ClientType::class, $modelToCompare);

        // 3. Soumission des données
        $form->submit($formData);

        // 4. Assertions
        $this->assertTrue($form->isSynchronized(), 'Le formulaire doit se synchroniser sans erreur.');
        $this->assertEquals('Entreprise Test GESIC', $modelToCompare->getRaisonSocial());
        $this->assertEquals('contact@testgesic.fr', $modelToCompare->getEmail());

        // Utilisation de isTopActif() au lieu de getTopActif()
        $this->assertTrue($modelToCompare->isTopActif());
    }
}
