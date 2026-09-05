<?php

namespace App\Controller;

// 1. IMPORTATION DES DÉPENDANCES (Entities, Repositories, Services Symfony, Mercure)
use App\Entity\Message;
use App\Entity\Ticket;
use App\Entity\Utilisateur;
use App\Enum\StatutTache;
use App\Form\NvxTicketType;
use App\Form\TicketType;
use App\Form\MessageType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use App\Enum\StatutTicket;
use App\Enum\StatutTicket as EnumStatutTicket;
use App\Repository\UtilisateurRepository;
use App\Repository\TacheRepository;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

// Route de base appliquée à toutes les méthodes du contrôleur
#[Route('/')]
final class TicketController extends AbstractController
{
    /**
     * Page d'accueil / Liste des tickets
     * Affiche une vue différente (Support ou Client) selon le rôle de l'utilisateur connecté.
     */
    #[Route(name: 'app_ticket_index', methods: ['GET'])]
    public function index(
        TicketRepository $ticketRepository,
        UtilisateurRepository $utilisateurRepository,
        TacheRepository $tacheRepository,
        Authorization $authorization,
        Request $request
    ): Response {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();
        $roles = $user->getRoles();

        // Génération du cookie de sécurité pour les notifications en temps réel avec Mercure
        $authorization->setCookie($request, ['ticket/liste']);

        // Vue réservée au personnel de support (Admin / Développeur)
        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_DEVELOPPEUR', $roles)) {
            return $this->render('ticket/vue_support.html.twig', [
                'tickets' => $ticketRepository->findAll(),
                'statuts' => StatutTicket::cases(),
                'nouveauxtickets' => $ticketRepository->findNonAssignes(),
                'ticketsassignes' => $ticketRepository->findByAssigne($user),
                'developpeurs' => $utilisateurRepository->findEquipeSupport(),
                'taches' => $tacheRepository->findAll(),
                'nbTachesAFaire' => $tacheRepository->countByStatut(StatutTache::A_FAIRE),
                'nbTicketsOuverts' => $ticketRepository->countTicketsParStatut(StatutTicket::NOUVEAU),
                'nbTicketsEnCours' => $ticketRepository->countTicketsParStatut(StatutTicket::EN_COURS),
                'nbTicketsUrgents' => $ticketRepository->countUrgents(),
            ]);
        }

        // Vue réservée aux clients (uniquement leurs propres tickets)
        return $this->render('ticket/vue_client.html.twig', [
            'nouveauxtickets' => $ticketRepository->findBy([
                'createur' => $user,
                'statut'   => StatutTicket::NOUVEAU->value,
            ]),
            'tickets' => $ticketRepository->findByClientSansNouveau($user),
            'statuts' => StatutTicket::cases(),
            'taches' => $tacheRepository->findAll(),
        ]);
    }
    /**
     * Page d'accueil / Liste des tickets
     * Affiche tous les tickets nouveaux
     */
    #[Route('/ticket/touslestickets', name: 'app_ticket_all', methods: ['GET'])]
    public function ticketAll(
        TicketRepository $ticketRepository,
        UtilisateurRepository $utilisateurRepository,
        TacheRepository $tacheRepository,
        Authorization $authorization,
        Request $request
    ): Response {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();
        $roles = $user->getRoles();

        // Génération du cookie de sécurité pour les notifications en temps réel avec Mercure
        $authorization->setCookie($request, ['ticket/liste']);

        // Vue réservée au personnel de support (Admin / Développeur)
        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_DEVELOPPEUR', $roles)) {
            return $this->render('ticket/vue_ticket_all.html.twig', [
                'tickets' => $ticketRepository->findAll(),
                'statuts' => StatutTicket::cases(),
                'nouveauxtickets' => $ticketRepository->findNonAssignes(),
                'ticketsassignes' => $ticketRepository->findByAssigne($user),
                'developpeurs' => $utilisateurRepository->findEquipeSupport(),
                'taches' => $tacheRepository->findAll(),
                'nbTachesAFaire' => $tacheRepository->countByStatut(StatutTache::A_FAIRE),
                'nbTicketsOuverts' => $ticketRepository->countTicketsParStatut(StatutTicket::NOUVEAU),
                'nbTicketsEnCours' => $ticketRepository->countTicketsParStatut(StatutTicket::EN_COURS),
                'nbTicketsUrgents' => $ticketRepository->countUrgents(),
            ]);
        }

        // Vue réservée aux clients (uniquement leurs propres tickets)
        return $this->render('ticket/vue_ticket_all.html.twig', [
            'nouveauxtickets' => $ticketRepository->findBy([
                'createur' => $user,
                'statut'   => StatutTicket::NOUVEAU->value,
            ]),
            'tickets' => $ticketRepository->findByClientSansNouveau($user),
            'statuts' => StatutTicket::cases(),
            'taches' => $tacheRepository->findAll(),
        ]);
    }

    /**
     * Historique global de tous les tickets (Réservé au Support/Admin)
     */
    #[Route('ticket/historique', name: 'app_ticket_historique', methods: ['GET'])]
    #[IsGranted(new Expression('is_granted("ROLE_DEVELOPPEUR") or is_granted("ROLE_ADMIN")'))]
    public function historique(
        TicketRepository $ticketRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        return $this->render('ticket/historiquetickets.html.twig', [
            'tickets' => $ticketRepository->findAllAvecRelations(),
            'statuts' => StatutTicket::cases(),
            'developpeurs' => $utilisateurRepository->findEquipeSupport(),
        ]);
    }

    /**
     * Historique personnel des tickets du client connecté
     */
    #[Route('ticket/historiqueclient', name: 'app_ticket_historiqueclient', methods: ['GET'])]
    #[IsGranted('ROLE_CLIENT')]
    public function historiqueClient(
        TicketRepository $ticketRepository
    ): Response {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        return $this->render('ticket/historiqueticketsClient.html.twig', [
            'tickets' => $ticketRepository->findByClientSansNouveau($user),
            'statuts' => EnumStatutTicket::cases(),
        ]);
    }

    /**
     * Affiche la charge de travail / la liste des tickets assignés à un développeur spécifique
     */
    #[Route('ticket/details-ticket-utilisateur/{id}', name: 'details_ticket_utilisateur')]
    public function detailsTicketUtilisateur(int $id, UtilisateurRepository $utilisateurRepository, TicketRepository $ticketRepo): Response
    {
        $developpeur = $utilisateurRepository->find($id);
        $tickets = $ticketRepo->findBy(['assigne' => $developpeur]);
        $nbTickets = count($tickets);
        $maxTickets = 10;
        $pourcentage = min(100, round(($nbTickets / $maxTickets) * 100));
        return $this->render('ticket/detailsTicketParUtilisateur.html.twig', [
            'developpeur' => $developpeur,
            'tickets'     => $tickets,
            'nbTickets'   => $nbTickets,
            'pourcentage' => $pourcentage,
        ]);
    }

    /**
     * Création d'un nouveau ticket par le client
     * Calcule automatiquement la priorité (impact x urgence) avant sauvegarde.
     */
    #[Route('ticket/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, HubInterface $hub): Response
    {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();

        $ticket = new Ticket();
        $ticket->setCreateur($user);

        $idClient = $user->getClient()?->getId() ?? 2;

        $form = $this->createForm(NvxTicketType::class, $ticket, [
            'id_client' => $idClient,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Calcul du score de priorité métier
            $score = $ticket->getImpact()->getNote() * $ticket->getUrgence()->getNote();
            $ticket->setPrioriteCalculee($score);

            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Ticket créé avec succès !');

            // --- PUBLICATION MERCURE ---
            $update = new Update(
                'ticket/liste',
                json_encode([
                    'id' => $ticket->getId(),
                    'titre' => $ticket->getTitre(),
                    'description' => $ticket->getDescription(),
                    'statut' => 'nouveau', // Ou la valeur exacte de votre statut (ex: $ticket->getStatut()->getValue())
                    'libellePriorite' => $ticket->getPrioriteCalculee(), // Ou le libellé formaté
                    'dateCreation' => $ticket->getDateCreation()->format('d/m/Y H:i'),
                ])
            );

            $hub->publish($update);
            // ---------------------------

            return $this->redirectToRoute('app_ticket_index');
        }

        return $this->render('ticket/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Modification d'un ticket et notification en temps réel via le Hub Mercure
     */
    #[Route('ticket/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Ticket $ticket,
        EntityManagerInterface $entityManager,
        HubInterface $hub
    ): Response {
        $form = $this->createForm(TicketType::class, $ticket, [
            'id_client' => $ticket->getLogicielClient()->getClient()->getId() ?? 2,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($ticket->getStatut() === StatutTicket::NOUVEAU) {
                $ticket->setAssigne(null);
            }

            $entityManager->flush();

            // Publication d'un événement Mercure pour mettre à jour l'interface des autres utilisateurs sans recharger la page
            $hub->publish(new Update(
                'ticket/liste',
                json_encode([
                    'id'              => $ticket->getId(),
                    'statut'          => $ticket->getStatut()->value,
                    'assigne'         => $ticket->getAssigne()?->getPrenom() . ' ' . $ticket->getAssigne()?->getNom(),
                    'assigneId'       => $ticket->getAssigne()?->getId(),
                    'titre'           => $ticket->getTitre(),
                    'description'     => $ticket->getDescription(),
                    'dateCreation'    => $ticket->getDateCreation()->format('d/m/Y'),
                    'libellePriorite' => $ticket->getLibellePriorite(),
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

    /**
     * Action AJAX (API) permettant à un membre du support d'assigner un ticket à lui-même
     */
    #[Route('ticket/{id}/prendre-en-charge', name: 'app_ticket_prendre', methods: ['POST'])]
    #[IsGranted(new Expression('is_granted("ROLE_DEVELOPPEUR") or is_granted("ROLE_ADMIN")'))]
    public function prendreEnCharge(
        Ticket $ticket,
        EntityManagerInterface $em,
        HubInterface $hub
    ): JsonResponse {

        $user = $this->getUser();

        if (!$user instanceof Utilisateur) {
            return $this->json(['success' => false, 'error' => 'Utilisateur introuvable'], 404);
        }

        // Évite le traitement en double si le ticket est déjà en cours
        if ($ticket->getStatut() === StatutTicket::EN_COURS && $ticket->getAssigne() !== null) {
            return $this->json(['success' => true, 'alreadyProcessed' => true]);
        }

        $ticket->setStatut(StatutTicket::EN_COURS);
        $ticket->setAssigne($user);
        $em->flush();

        // Notification Mercure
        $hub->publish(new Update(
            'ticket/liste',
            json_encode([
                'id'              => $ticket->getId(),
                'statut'          => $ticket->getStatut()->value,
                'assigne'         => $user->getPrenom() . ' ' . $user->getNom(),
                'assigneId'       => $ticket->getAssigne()?->getId(),
                'titre'           => $ticket->getTitre(),
                'description'     => $ticket->getDescription(),
                'dateCreation'    => $ticket->getDateCreation()->format('d/m/Y'),
                'libellePriorite' => $ticket->getLibellePriorite(),
            ])
        ));

        return $this->json(['success' => true]);
    }

    /**
     * Affichage détaillé d'un ticket et de son fil de discussion (messages)
     * Gère également le traitement du formulaire d'envoi de message (avec support des réponses).
     */
    #[Route('ticket/{id}', name: 'app_ticket_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Ticket $ticket,
        EntityManagerInterface $entityManager
    ): Response {
        // Formulaire d'ajout de message
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setTicket($ticket);
            /** @var \App\Entity\Utilisateur $user */
            $user = $this->getUser();
            $message->setUtilisateur($user);

            // Gestion des réponses imbriquées (message parent)
            $parentId = $request->request->get('parent_id');
            if ($parentId) {
                $parent = $entityManager->getRepository(Message::class)->find($parentId);
                $message->setMessageParent($parent);
            }

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        // Récupération de la liste des messages principaux
        $messages = $entityManager->getRepository(Message::class)->findBy(
            ['ticket' => $ticket, 'messageParent' => null, 'topActif' => true],
            ['dateEnvoi' => 'ASC']
        );

        return $this->render('ticket/show.html.twig', [
            'ticket'   => $ticket,
            'messages' => $messages,
            'form'     => $form,
        ]);
    }

    /**
     * Suppression d'un ticket sécurisée par un jeton CSRF
     */
    #[Route('ticket/{id}', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
    }
}
