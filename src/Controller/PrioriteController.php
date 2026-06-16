<?php

namespace App\Controller;

use App\Entity\Priorite;
use App\Form\PrioriteType;
use App\Repository\PrioriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/priorite')]
final class PrioriteController extends AbstractController
{
    #[Route(name: 'app_priorite_index', methods: ['GET'])]
    public function index(PrioriteRepository $prioriteRepository): Response
    {
        return $this->render('priorite/index.html.twig', [
            'priorites' => $prioriteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_priorite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $priorite = new Priorite();
        $form = $this->createForm(PrioriteType::class, $priorite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($priorite);
            $entityManager->flush();

            return $this->redirectToRoute('app_priorite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('priorite/new.html.twig', [
            'priorite' => $priorite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_priorite_show', methods: ['GET'])]
    public function show(Priorite $priorite): Response
    {
        return $this->render('priorite/show.html.twig', [
            'priorite' => $priorite,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_priorite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Priorite $priorite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrioriteType::class, $priorite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_priorite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('priorite/edit.html.twig', [
            'priorite' => $priorite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_priorite_delete', methods: ['POST'])]
    public function delete(Request $request, Priorite $priorite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$priorite->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($priorite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_priorite_index', [], Response::HTTP_SEE_OTHER);
    }
}
