<?php

namespace App\Controller;


use App\Entity\Ticket;
use App\Form\NvxTicketType;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use App\Enum\StatutTicket;

#[Route('/ticket')]
final class TicketController extends AbstractController
{
    #[Route(name: 'app_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $ticketRepository->findAll(),
            'statuts' => StatutTicket::cases(),
        ]);
    }

    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UtilisateurRepository $ur): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(NvxTicketType::class, $ticket, [
            'id_client' => 2,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fakeUser = $ur->find(1);
            $ticket->setCreateur($fakeUser);

            // Calcul priorité = note impact × note urgence
            $score = $ticket->getImpact()->getNote() * $ticket->getUrgence()->getNote();
            $ticket->setPrioriteCalculee($score);

            $em->persist($ticket);
            $em->flush();

            return $this->redirectToRoute('app_ticket_index');
        }

        return $this->render('ticket/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Ticket $ticket,
        EntityManagerInterface $entityManager,
        HubInterface $hub
    ): Response {
        $form = $this->createForm(TicketType::class, $ticket, [
            'id_client' => $ticket->getLogicielClient()?->getClient()->getId() ?? 2,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Publie la mise à jour à tous les abonnés
            $hub->publish(new Update(
                'ticket/liste',
                json_encode([
                    'id'      => $ticket->getId(),
                    'statut'  => $ticket->getStatut()->value,
                    'assigne' => $ticket->getAssigne()?->getPrenom() . ' ' . $ticket->getAssigne()?->getNom(),
                ])
            ));

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket'  => $ticket,
            'form'    => $form,
            'statuts' => StatutTicket::cases(),
        ]);
    }
    #[Route('/{id}/prendre-en-charge', name: 'app_ticket_prendre', methods: ['POST'])]
    public function prendreEnCharge(
        Ticket $ticket,
        EntityManagerInterface $em,
        HubInterface $hub,
        UtilisateurRepository $ur
    ): JsonResponse {
        /** @var \App\Entity\Utilisateur $fakeUser */
        $fakeUser = $ur->find(1);

        if (!$fakeUser) {
            return $this->json(['success' => false, 'error' => 'Utilisateur introuvable'], 404);
        }

        $ticket->setStatut(StatutTicket::EN_COURS);
        $ticket->setAssigne($fakeUser);
        $em->flush();

        $hub->publish(new Update(
            'ticket/liste',
            json_encode([
                'id'      => $ticket->getId(),
                'statut'  => $ticket->getStatut()->value,
                'assigne' => $fakeUser->getPrenom() . ' ' . $fakeUser->getNom(),
            ])
        ));

        return $this->json(['success' => true]);
    }

    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
    }
}
