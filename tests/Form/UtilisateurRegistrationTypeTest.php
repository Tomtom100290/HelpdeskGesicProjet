<?php

namespace App\Tests\Form;

use App\Entity\Utilisateur;
use App\Enum\Role;
use App\Form\UtilisateurRegistrationType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UtilisateurRegistrationTypeTest extends KernelTestCase
{
    public function testSubmitValidData(): void
    {
        self::bootKernel();
        $formFactory = static::getContainer()->get('form.factory');

        $formData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'role' => Role::CLIENT->value,
            'plainPassword' => [
                'first' => 'MotDePasseSecurise123!',
                'second' => 'MotDePasseSecurise123!',
            ],
            'numTel' => '0601020304',
        ];

        $modelToCompare = new Utilisateur();
        $form = $formFactory->create(UtilisateurRegistrationType::class, $modelToCompare);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized(), 'Le formulaire UtilisateurRegistrationType doit se synchroniser sans erreur.');
        $this->assertEquals('Dupont', $modelToCompare->getNom());
        $this->assertEquals('Jean', $modelToCompare->getPrenom());
        $this->assertEquals('jean.dupont@example.com', $modelToCompare->getEmail());
        $this->assertEquals(Role::CLIENT, $modelToCompare->getRole());
        $this->assertEquals('MotDePasseSecurise123!', $form->get('plainPassword')->getData());
    }
}
