<?php

namespace App\Controller;

use App\Entity\Logiciel;
use App\Form\LogicielType;
use App\Repository\LogicielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/logiciel')]
final class LogicielController extends AbstractController
{
    #[Route(name: 'app_logiciel_index', methods: ['GET'])]
    public function index(LogicielRepository $logicielRepository): Response
    {
        return $this->render('logiciel/index.html.twig', [
            'logiciels' => $logicielRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_logiciel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $logiciel = new Logiciel();
        $form = $this->createForm(LogicielType::class, $logiciel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($logiciel);
            $entityManager->flush();

            return $this->redirectToRoute('app_logiciel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('logiciel/new.html.twig', [
            'logiciel' => $logiciel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_logiciel_show', methods: ['GET'])]
    public function show(Logiciel $logiciel): Response
    {
        return $this->render('logiciel/show.html.twig', [
            'logiciel' => $logiciel,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_logiciel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Logiciel $logiciel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LogicielType::class, $logiciel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_logiciel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('logiciel/edit.html.twig', [
            'logiciel' => $logiciel,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_logiciel_delete', methods: ['POST'])]
    public function delete(Request $request, Logiciel $logiciel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$logiciel->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($logiciel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_logiciel_index', [], Response::HTTP_SEE_OTHER);
    }
}
