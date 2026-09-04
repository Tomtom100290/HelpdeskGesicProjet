<?php

namespace App\Tests\Form;

use App\Entity\Logiciel;
use App\Form\LogicielType;
use Symfony\Component\Form\Test\TypeTestCase;

class LogicielTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        // 1. Jeu de données simulant la saisie du formulaire
        $formData = [
            'libelle'         => 'GESIC Helpdesk',
            'description'     => 'Logiciel de gestion de tickets de support',
            'typeLogiciel'    => 'SaaS',
            'coeffCriticite' => 3,
            'topActif'        => true,
        ];

        // 2. Instanciation du modèle et du formulaire
        $modelToCompare = new Logiciel();
        $form = $this->factory->create(LogicielType::class, $modelToCompare);

        // 3. Soumission des données
        $form->submit($formData);

        // 4. Assertions
        $this->assertTrue($form->isSynchronized(), 'Le formulaire LogicielType doit se synchroniser sans erreur.');
        $this->assertEquals('GESIC Helpdesk', $modelToCompare->getLibelle());
        $this->assertEquals('Logiciel de gestion de tickets de support', $modelToCompare->getDescription());
        $this->assertEquals(3, $modelToCompare->getCoeffCriticite());
    }
}
