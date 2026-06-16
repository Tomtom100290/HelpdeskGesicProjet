<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TableauDeBordController extends AbstractController
{
    #[Route('/tableaudebord', name: 'app_tableau_de_bord')]
    public function index(): Response
    {
        return $this->render('tableau_de_bord/vueclient.html.twig', [
            'controller_name' => 'TableauDeBordController',
        ]);
    }
}
