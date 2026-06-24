<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // 1. Si l'utilisateur est connecté, on analyse son rôle et on le redirige
        if ($this->getUser()) {
            $roles = $this->getUser()->getRoles();

            // Si c'est un ADMIN ou un DEVELOPPEUR -> direction la liste des tickets
            if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_DEVELOPPEUR', $roles)) {
                return $this->redirectToRoute('app_ticket_index');
            }

            // Si c'est un CLIENT -> direction sa page d'accueil (Remplace par le nom exact de ta route client)
            if (in_array('ROLE_CLIENT', $roles)) {
                return $this->redirectToRoute('app_ticket_index');
            }

            // Route de secours par défaut si aucun rôle ne correspond
            return $this->redirectToRoute('app_home');
        }

        // 2. Gestion de l'affichage du formulaire s'il n'est pas encore connecté
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
