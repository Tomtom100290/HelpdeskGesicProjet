<?php

namespace App\Tests\Form;

use App\Entity\Tache;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Enum\StatutTache;
use App\Form\TacheType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TacheTypeTest extends KernelTestCase
{
    public function testSubmitValidData(): void
    {
        self::bootKernel();
        $formFactory = static::getContainer()->get('form.factory');

        // Instanciation de faux objets Doctrine pour simuler les associations
        $ticket = new Ticket();
        $utilisateurAssigne = new Utilisateur();
        $utilisateurCreateur = new Utilisateur();

        $formData = [
            'libelle' => 'Tâche de test',
            'description' => 'Description de la tâche',
            'dateRealisation' => '2026-09-04',
            'statut' => StatutTache::A_FAIRE, // Assurez-vous d'utiliser une case valide de votre Enum
            'ticket' => $ticket,
            'utilisateurAssigne' => $utilisateurAssigne,
            'utilisateurCreateur' => $utilisateurCreateur,
        ];

        $modelToCompare = new Tache();
        $form = $formFactory->create(TacheType::class, $modelToCompare);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized(), 'Le formulaire TacheType doit se synchroniser correctement.');
        $this->assertEquals('Tâche de test', $modelToCompare->getLibelle());
        $this->assertEquals('Description de la tâche', $modelToCompare->getDescription());
    }
}
