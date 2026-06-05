<?php

namespace App\Controller;

use App\Entity\Impact;
use App\Form\ImpactType;
use App\Repository\ImpactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/impact')]
final class ImpactController extends AbstractController
{
    #[Route(name: 'app_impact_index', methods: ['GET'])]
    public function index(ImpactRepository $impactRepository): Response
    {
        return $this->render('impact/index.html.twig', [
            'impacts' => $impactRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_impact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $impact = new Impact();
        $form = $this->createForm(ImpactType::class, $impact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($impact);
            $entityManager->flush();

            return $this->redirectToRoute('app_impact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('impact/new.html.twig', [
            'impact' => $impact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_impact_show', methods: ['GET'])]
    public function show(Impact $impact): Response
    {
        return $this->render('impact/show.html.twig', [
            'impact' => $impact,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_impact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Impact $impact, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImpactType::class, $impact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_impact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('impact/edit.html.twig', [
            'impact' => $impact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_impact_delete', methods: ['POST'])]
    public function delete(Request $request, Impact $impact, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$impact->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($impact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_impact_index', [], Response::HTTP_SEE_OTHER);
    }
}
