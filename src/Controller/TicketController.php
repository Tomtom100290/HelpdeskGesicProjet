<?php

namespace App\Controller;

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
use App\Repository\UtilisateurRepository;
use App\Repository\TacheRepository;
use Symfony\Component\Mercure\Authorization;

#[Route('/')]
final class TicketController extends AbstractController
{
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

        // Génère un cookie d'autorisation Mercure pour le topic voulu
        $authorization->setCookie($request, ['ticket/liste']);

        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_DEVELOPPEUR', $roles)) {
            return $this->render('ticket/vuesupport.html.twig', [
                'tickets' => $ticketRepository->findAll(),
                'statuts' => StatutTicket::cases(),
                'nouveauxtickets' => $ticketRepository->findNonAssignes(),
                'ticketsassignes' => $ticketRepository->findByAssigne($user),
                'developpeurs' => $utilisateurRepository->findEquipeSupport(),
                'taches' => $tacheRepository->findAll(),
                'nbTachesAFaire' => $tacheRepository->countByStatut(StatutTache::A_FAIRE),
            ]);
        }

        return $this->render('ticket/vueclient.html.twig', [
            'nouveauxtickets' => $ticketRepository->findBy([
                'createur' => $user,
                'statut'   => StatutTicket::NOUVEAU->value,
            ]),
            'tickets' => $ticketRepository->findByClientSansNouveau($user),
            'statuts' => StatutTicket::cases(),
            'taches' => $tacheRepository->findAll(),
        ]);
    }
    /*Affichage de l'historique des tickets*/
    #[Route('/historique', name: 'app_ticket_historique', methods: ['GET'])]
    public function historique(
        TicketRepository $ticketRepository,
        UtilisateurRepository $utilisateurRepository
    ): Response {
        return $this->render('ticket/historiquetickets.html.twig', [
            // Utilisation de ta méthode optimisée
            'tickets' => $ticketRepository->findAllAvecRelations(),
            'statuts' => StatutTicket::cases(),
            'developpeurs' => $utilisateurRepository->findEquipeSupport(),
        ]);
    }
    #[Route('/details-ticket-utilisateur/{id}', name: 'details_ticket_utilisateur')]
    public function detailsTicketUtilisateur(int $id, UtilisateurRepository $utilisateurRepository, TicketRepository $ticketRepo): Response
    {
        $developpeur = $utilisateurRepository->find($id);
        $tickets = $ticketRepo->findBy(['assigne' => $developpeur]);
        $nbTickets = count($tickets);
        $maxTickets = 10; // à adapter selon ta logique
        $pourcentage = $maxTickets > 0 ? min(100, round(($nbTickets / $maxTickets) * 100)) : 0;

        return $this->render('ticket/detailsTicketParUtilisateur.html.twig', [
            'developpeur' => $developpeur,
            'tickets'     => $tickets,
            'nbTickets'   => $nbTickets,
            'pourcentage' => $pourcentage,
        ]);
    }

    /*#[Route('/espacetickets', name: 'app_espace_ticket', methods: ['GET', 'POST'])]
    public function espaceticket(Request $request, EntityManagerInterface $em): Response {}
*/
    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();

        $ticket = new Ticket();

        // On récupère dynamiquement l'id du client lié à l'utilisateur connecté s'il existe
        $idClient = $user->getClient()?->getId() ?? 2;

        $form = $this->createForm(NvxTicketType::class, $ticket, [
            'id_client' => $idClient,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // plus de fakeUser ! On associe l'utilisateur connecté
            $ticket->setCreateur($user);

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
            // Enlève l'assignement si le ticket repasse à "NOUVEAU"
            if ($ticket->getStatut() === StatutTicket::NOUVEAU) {
                $ticket->setAssigne(null);
            }

            $entityManager->flush();

            // Publie la mise à jour à tous les abonnés
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

    #[Route('/{id}/prendre-en-charge', name: 'app_ticket_prendre', methods: ['POST'])]
    public function prendreEnCharge(
        Ticket $ticket,
        EntityManagerInterface $em,
        HubInterface $hub
    ): JsonResponse {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['success' => false, 'error' => 'Utilisateur introuvable'], 404);
        }

        // Sécurité anti double-soumission : si déjà assigné et en cours, ne republie pas
        if ($ticket->getStatut() === StatutTicket::EN_COURS && $ticket->getAssigne() !== null) {
            return $this->json(['success' => true, 'alreadyProcessed' => true]);
        }

        $ticket->setStatut(StatutTicket::EN_COURS);
        $ticket->setAssigne($user);
        $em->flush();

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
    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Ticket $ticket,
        EntityManagerInterface $entityManager
    ): Response {
        // Nouveau message
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setTicket($ticket);
            $message->setUtilisateur($this->getUser());

            // Si c'est une réponse à un message existant
            $parentId = $request->request->get('parent_id');
            if ($parentId) {
                $parent = $entityManager->getRepository(Message::class)->find($parentId);
                $message->setMessageParent($parent);
            }

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        // Récupère uniquement les messages racines (sans parent)
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
