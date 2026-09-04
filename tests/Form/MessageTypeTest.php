<?php

namespace App\Tests\Form;

use App\Entity\Message;
use App\Form\MessageType;
use Symfony\Component\Form\Test\TypeTestCase;

class MessageTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        // 1. Jeu de données de test
        $formData = [
            'contenu' => 'Ceci est un message de test pour valider le formulaire.',
        ];

        // 2. Instanciation du modèle et création du formulaire
        $modelToCompare = new Message();
        $form = $this->factory->create(MessageType::class, $modelToCompare);

        // 3. Soumission des données
        $form->submit($formData);

        // 4. Assertions
        $this->assertTrue($form->isSynchronized(), 'Le formulaire MessageType doit se synchroniser sans erreur.');
        $this->assertEquals('Ceci est un message de test pour valider le formulaire.', $modelToCompare->getContenu());
    }
}
