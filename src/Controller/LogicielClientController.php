<?php

namespace App\Controller;

use App\Entity\LogicielClient;
use App\Form\LogicielClientType;
use App\Repository\LogicielClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/logicielclient')]
final class LogicielClientController extends AbstractController
{
    #[Route(name: 'app_logiciel_client_index', methods: ['GET'])]
    public function index(LogicielClientRepository $logicielClientRepository): Response
    {
        $logicielClients = $logicielClientRepository->findAll();

        // Regroupement par client, puis par logiciel
        $parClient = [];
        foreach ($logicielClients as $lc) {
            $clientId = $lc->getClient()->getId();

            if (!isset($parClient[$clientId])) {
                $parClient[$clientId] = [
                    'client' => $lc->getClient(),
                    'logiciels' => [],
                ];
            }

            $logicielId = $lc->getLogiciel()->getId();

            if (!isset($parClient[$clientId]['logiciels'][$logicielId])) {
                $parClient[$clientId]['logiciels'][$logicielId] = [
                    'logiciel' => $lc->getLogiciel(),
                    'versions' => [],
                ];
            }

            $parClient[$clientId]['logiciels'][$logicielId]['versions'][] = $lc->getVersionLogiciel();
        }

        return $this->render('logiciel_client/index.html.twig', [
            'clients_data' => $parClient,
        ]);
    }

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

    #[Route('/{id}', name: 'app_logiciel_client_show', methods: ['GET'])]
    public function show(LogicielClient $logicielClient): Response
    {
        return $this->render('logiciel_client/show.html.twig', [
            'logiciel_client' => $logicielClient,
        ]);
    }

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
