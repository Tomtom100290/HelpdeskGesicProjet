<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\InstallationMultipleDTO;
use App\Entity\Logiciel;
use App\Entity\LogicielClient;
use App\Form\InstallationMultipleType;
use App\Form\LogicielType;
use App\Repository\LogicielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur de gestion du catalogue de logiciels et de leur déploiement.
 *
 * Ce contrôleur permet d'énumérer, créer, modifier, afficher et supprimer des logiciels.
 * Il intègre également le déploiement multiple d'un logiciel vers plusieurs clients via un DTO.
 * L'accès est restreint aux utilisateurs possédant le rôle DEVELOPPEUR ou ADMIN.
 */
#[Route('/logiciel')]
#[IsGranted(new Expression('is_granted("ROLE_DEVELOPPEUR") or is_granted("ROLE_ADMIN")'))]
final class LogicielController extends AbstractController
{
    /**
     * Affiche la liste complète des logiciels enregistrés.
     *
     * @param LogicielRepository $logicielRepository Le dépôt pour accéder aux données des logiciels.
     *
     * @return Response La vue du catalogue de logiciels.
     */
    #[Route(name: 'app_logiciel_index', methods: ['GET'])]
    public function index(LogicielRepository $logicielRepository): Response
    {
        return $this->render('logiciel/index.html.twig', [
            'logiciels' => $logicielRepository->findAll(),
        ]);
    }

    /**
     * Traite la création d'un nouveau logiciel dans le catalogue.
     *
     * @param Request                $request       La requête HTTP entrante.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour enregistrer l'entité.
     *
     * @return Response Redirection vers l'index ou rendu du formulaire de création.
     */
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

    /**
     * Gère le déploiement simultané d'un logiciel auprès de plusieurs clients.
     *
     * Note de routage : Cette route fixe `/deployer` est déclarée avant `/{id}`
     * pour éviter que la chaîne "deployer" ne soit interprétée comme un identifiant.
     *
     * @param Request                $request La requête HTTP contenant les données saisies.
     * @param EntityManagerInterface $em      L'ORM Doctrine pour la création des liaisons LogicielClient.
     *
     * @return Response Redirection avec message flash ou affichage du formulaire DTO.
     */
    #[Route('/deployer', name: 'app_logiciel_deployer', methods: ['GET', 'POST'])]
    public function deployer(Request $request, EntityManagerInterface $em): Response
    {
        $dto = new InstallationMultipleDTO();
        $form = $this->createForm(InstallationMultipleType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\Client $client */
            foreach ($dto->clients as $client) {
                $installation = new LogicielClient();

                $installation->setLogiciel($dto->logiciel);
                $installation->setClient($client);
                $installation->setVersionLogiciel($dto->version);

                $em->persist($installation);
            }
            $em->flush();

            $this->addFlash('success', 'Le déploiement a bien été enregistré.');
            return $this->redirectToRoute('app_logiciel_deployer');
        }

        return $this->render('logiciel/deployer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche les détails d'un logiciel spécifique.
     *
     * @param Logiciel $logiciel L'entité Logiciel injectée automatiquement par ParamConverter.
     *
     * @return Response La vue détaillée du logiciel.
     */
    #[Route('/{id}', name: 'app_logiciel_show', methods: ['GET'])]
    public function show(Logiciel $logiciel): Response
    {
        return $this->render('logiciel/show.html.twig', [
            'logiciel' => $logiciel,
        ]);
    }

    /**
     * Traite la modification des informations d'un logiciel existant.
     *
     * @param Request                $request       La requête HTTP entrante.
     * @param Logiciel               $logiciel      Le logiciel à modifier.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour la mise à jour en BDD.
     *
     * @return Response Redirection vers la liste ou rendu du formulaire d'édition.
     */
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

    /**
     * Supprime un logiciel du catalogue après validation du jeton CSRF.
     *
     * @param Request                $request       La requête HTTP contenant le jeton CSRF.
     * @param Logiciel               $logiciel      Le logiciel à supprimer.
     * @param EntityManagerInterface $entityManager L'ORM Doctrine pour l'opération de suppression.
     *
     * @return Response Redirection vers la liste des logiciels.
     */
    #[Route('/{id}', name: 'app_logiciel_delete', methods: ['POST'])]
    public function delete(Request $request, Logiciel $logiciel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $logiciel->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($logiciel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_logiciel_index', [], Response::HTTP_SEE_OTHER);
    }
}
