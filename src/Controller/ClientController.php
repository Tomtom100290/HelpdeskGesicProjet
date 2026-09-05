<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Client;
use App\Entity\LogicielClient;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur de gestion des clients du système.
 *
 * Ce contrôleur gère le CRUD complet pour la fiche client, ainsi que l'affichage
 * des logiciels qui lui sont actuellement associés via la table pivot LogicielClient.
 * L'accès est restreint aux utilisateurs possédant le rôle DEVELOPPEUR ou ADMIN.
 */
#[Route('/client')]
#[IsGranted(new Expression('is_granted("ROLE_DEVELOPPEUR") or is_granted("ROLE_ADMIN")'))]
final class ClientController extends AbstractController
{
    /**
     * Affiche la liste complète des clients.
     *
     * @param ClientRepository $clientRepository Le dépôt d'accès aux entités Client.
     *
     * @return Response La vue listant l'ensemble des clients.
     */
    #[Route(name: 'app_client_index', methods: ['GET'])]
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->render('client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    /**
     * Traite la création d'un nouveau client.
     *
     * @param Request                $request       La requête HTTP entrante.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour enregistrer le client.
     *
     * @return Response Redirection vers l'index en cas de succès, ou affichage du formulaire.
     */
    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    /**
     * Affiche la fiche détaillée d'un client et la liste de ses logiciels installés.
     *
     * @param Client                 $client L'entité Client injectée via ParamConverter.
     * @param EntityManagerInterface $em     L'ORM Doctrine pour récupérer les associations LogicielClient.
     *
     * @return Response La vue de détail du client avec ses installations.
     */
    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client, EntityManagerInterface $em): Response
    {
        /** @var LogicielClient[] $logicielsInstalles */
        $logicielsInstalles = $em->getRepository(LogicielClient::class)->findBy([
            'client' => $client,
        ]);

        return $this->render('client/show.html.twig', [
            'client' => $client,
            'logicielClient' => $logicielsInstalles,
        ]);
    }

    /**
     * Traite la modification des informations d'un client existant.
     *
     * @param Request                $request       La requête HTTP entrante.
     * @param Client                 $client        Le client à modifier.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour enregistrer les modifications.
     *
     * @return Response Redirection vers l'index ou rendu du formulaire pré-rempli.
     */
    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    /**
     * Supprime un client du système après validation du jeton CSRF.
     *
     * @param Request                $request       La requête HTTP contenant le jeton CSRF.
     * @param Client                 $client        Le client à supprimer.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour exécuter la suppression.
     *
     * @return Response Redirection vers l'index des clients.
     */
    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
