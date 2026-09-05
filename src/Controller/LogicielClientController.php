<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\LogicielClient;
use App\Form\LogicielClientType;
use App\Repository\LogicielClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur de gestion des installations de logiciels chez les clients.
 *
 * Ce contrôleur gère la liaison N-N (LogicielClient) entre les clients, les logiciels
 * et leurs versions installées. L'accès est restreint aux rôles DEVELOPPEUR et ADMIN.
 */
#[Route('/logicielclient')]
#[IsGranted(new Expression('is_granted("ROLE_DEVELOPPEUR") or is_granted("ROLE_ADMIN")'))]
final class LogicielClientController extends AbstractController
{
    /**
     * Affiche la liste des logiciels installés, regroupés par client puis par logiciel.
     *
     * @param LogicielClientRepository $logicielClientRepository Le dépôt d'accès aux entités LogicielClient.
     *
     * @return Response La vue d'index structurée par client et par logiciel.
     */
    #[Route(name: 'app_logiciel_client_index', methods: ['GET'])]
    public function index(LogicielClientRepository $logicielClientRepository): Response
    {
        $logicielClients = $logicielClientRepository->findAll();

        /**
         * Structure du tableau multidimensionnel pour la vue Twig :
         * @var array<int, array{
         *     client: \App\Entity\Client,
         *     logiciels: array<int, array{
         *         logiciel: \App\Entity\Logiciel,
         *         versions: array<int, string|null>
         *     }>
         * }> $parClient
         */
        $parClient = [];

        foreach ($logicielClients as $lc) {
            $client = $lc->getClient();
            $logiciel = $lc->getLogiciel();

            if ($client === null || $logiciel === null) {
                continue;
            }

            $clientId = $client->getId();
            $logicielId = $logiciel->getId();

            if (!isset($parClient[$clientId])) {
                $parClient[$clientId] = [
                    'client' => $client,
                    'logiciels' => [],
                ];
            }

            if (!isset($parClient[$clientId]['logiciels'][$logicielId])) {
                $parClient[$clientId]['logiciels'][$logicielId] = [
                    'logiciel' => $logiciel,
                    'versions' => [],
                ];
            }

            $parClient[$clientId]['logiciels'][$logicielId]['versions'][] = $lc->getVersionLogiciel();
        }

        return $this->render('logiciel_client/index.html.twig', [
            'clients_data' => $parClient,
        ]);
    }

    /**
     * Traite l'affectation d'un logiciel à un client via un nouveau formulaire.
     *
     * @param Request                $request       La requête HTTP entrante.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour enregistrer l'installation.
     *
     * @return Response Redirection vers l'index ou affichage du formulaire de création.
     */
    #[Route('/new', name: 'app_logiciel_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $logicielClient = new LogicielClient();
        $form = $this->createForm(LogicielClientType::class, $logicielClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($logicielClient);
            $entityManager->flush();

            return $this->redirectToRoute('app_logiciel_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('logiciel_client/new.html.twig', [
            'logiciel_client' => $logicielClient,
            'form' => $form,
        ]);
    }

    /**
     * Affiche les détails d'une installation spécifique.
     *
     * @param LogicielClient $logicielClient L'entité récurrente injectée via le ParamConverter.
     *
     * @return Response La vue détaillée de l'installation.
     */
    #[Route('/{id}', name: 'app_logiciel_client_show', methods: ['GET'])]
    public function show(LogicielClient $logicielClient): Response
    {
        return $this->render('logiciel_client/show.html.twig', [
            'logiciel_client' => $logicielClient,
        ]);
    }

    /**
     * Traite la modification d'une installation (ex: changement de version).
     *
     * @param Request                $request        La requête HTTP entrante.
     * @param LogicielClient         $logicielClient L'installation à modifier.
     * @param EntityManagerInterface $entityManager  L'ORM Doctrine pour mettre à jour la BDD.
     *
     * @return Response Redirection vers la liste ou rendu du formulaire pré-rempli.
     */
    #[Route('/{id}/edit', name: 'app_logiciel_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LogicielClient $logicielClient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LogicielClientType::class, $logicielClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_logiciel_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('logiciel_client/edit.html.twig', [
            'logiciel_client' => $logicielClient,
            'form' => $form,
        ]);
    }

    /**
     * Supprime une association entre un logiciel et un client après validation du jeton CSRF.
     *
     * @param Request                $request        La requête HTTP contenant le jeton CSRF.
     * @param LogicielClient         $logicielClient L'entité à supprimer.
     * @param EntityManagerInterface $entityManager  L'ORM Doctrine pour la suppression en BDD.
     *
     * @return Response Redirection vers l'index des installations.
     */
    #[Route('/{id}', name: 'app_logiciel_client_delete', methods: ['POST'])]
    public function delete(Request $request, LogicielClient $logicielClient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $logicielClient->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($logicielClient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_logiciel_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
