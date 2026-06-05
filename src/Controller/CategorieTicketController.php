<?php

namespace App\Controller;

use App\Entity\CategorieTicket;
use App\Form\CategorieTicketType;
use App\Repository\CategorieTicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categorie-ticket')]
final class CategorieTicketController extends AbstractController
{
    #[Route(name: 'app_categorie_ticket_index', methods: ['GET'])]
    public function index(CategorieTicketRepository $categorieTicketRepository): Response
    {
        return $this->render('categorie_ticket/index.html.twig', [
            'categorie_tickets' => $categorieTicketRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorie_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorieTicket = new CategorieTicket();
        $form = $this->createForm(CategorieTicketType::class, $categorieTicket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorieTicket);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_ticket/new.html.twig', [
            'categorie_ticket' => $categorieTicket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_ticket_show', methods: ['GET'])]
    public function show(CategorieTicket $categorieTicket): Response
    {
        return $this->render('categorie_ticket/show.html.twig', [
            'categorie_ticket' => $categorieTicket,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategorieTicket $categorieTicket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieTicketType::class, $categorieTicket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie_ticket/edit.html.twig', [
            'categorie_ticket' => $categorieTicket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categorie_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, CategorieTicket $categorieTicket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorieTicket->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorieTicket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_ticket_index', [], Response::HTTP_SEE_OTHER);
    }
}
