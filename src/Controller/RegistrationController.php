<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1. On récupère le mot de passe en clair saisi dans le formulaire
            $plainPassword = $user->getMotDePasse();

            // 2. On le hache proprement en respectant la configuration de security.yaml
            $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);

            // 3. On réinjecte le mot de passe haché dans l'entité
            $user->setMotDePasse($hashedPassword);

            // 4. On sauvegarde en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le compte a bien été créé ! Vous pouvez vous connecter.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/inscription.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
