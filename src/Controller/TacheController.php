<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Tache;
use App\Enum\StatutTache;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur de gestion du CRUD des tâches d'intervention.
 *
 * Ce contrôleur permet d'énumérer, créer, afficher, éditer, valider la réalisation
 * et supprimer les tâches. L'accès est restreint aux rôles DEVELOPPEUR et ADMIN.
 */
#[Route('/tache')]
#[IsGranted(new Expression('is_granted("ROLE_DEVELOPPEUR") or is_granted("ROLE_ADMIN")'))]
final class TacheController extends AbstractController
{
    /**
     * Affiche la liste globale de toutes les tâches.
     *
     * @param TacheRepository $tacheRepository Le dépôt pour accéder aux entités Tache.
     *
     * @return Response La vue du tableau récapitulatif des tâches.
     */
    #[Route(name: 'app_tache_index', methods: ['GET'])]
    public function index(TacheRepository $tacheRepository): Response
    {
        return $this->render('tache/index.html.twig', [
            'taches' => $tacheRepository->findAll(),
        ]);
    }

    /**
     * Traite la création d'une nouvelle tâche via son formulaire dédié.
     *
     * @param Request                $request       La requête HTTP entrante.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour la persistance en BDD.
     *
     * @return Response Redirection vers la liste en cas de succès, ou le formulaire d'édition.
     */
    #[Route('/new', name: 'app_tache_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tache);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche créée avec succès !');

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/new.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    /**
     * Affiche le détail d'une tâche spécifique.
     *
     * @param Tache $tache L'entité Tache injectée automatiquement via le ParamConverter.
     *
     * @return Response La vue détaillée de la tâche.
     */
    #[Route('/{id}', name: 'app_tache_show', methods: ['GET'])]
    public function show(Tache $tache): Response
    {
        return $this->render('tache/show.html.twig', [
            'tache' => $tache,
        ]);
    }

    /**
     * Bascule le statut d'une tâche vers REALISEE après vérification du jeton CSRF.
     *
     * @param Request                $request       La requête HTTP contenant le jeton CSRF.
     * @param Tache                  $tache         La tâche à marquer comme réalisée.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour l'enregistrement.
     *
     * @return Response Redirection vers l'index des tickets.
     */
    #[Route('/{id}/effectuer', name: 'app_tache_effectuer', methods: ['POST'])]
    public function effectuer(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('effectuer' . $tache->getId(), $request->getPayload()->getString('_token'))) {
            $tache->setStatut(StatutTache::REALISEE);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Tâche marquée comme réalisée !');
        return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Traite la modification d'une tâche existante.
     *
     * @param Request                $request       La requête HTTP entrante.
     * @param Tache                  $tache         La tâche à modifier.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour mettre à jour la BDD.
     *
     * @return Response Redirection vers l'index ou rendu du formulaire pré-rempli.
     */
    #[Route('/{id}/edit', name: 'app_tache_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    /**
     * Supprime une tâche de la base de données après vérification du jeton CSRF.
     *
     * @param Request                $request       La requête HTTP contenant le jeton CSRF.
     * @param Tache                  $tache         La tâche à supprimer.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour la suppression.
     *
     * @return Response Redirection vers la liste des tâches.
     */
    #[Route('/{id}', name: 'app_tache_delete', methods: ['POST'])]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tache->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
    }
}
