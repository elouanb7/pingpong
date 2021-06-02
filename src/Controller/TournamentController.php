<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TournamentController extends AbstractController
{
    /**
     * @Route("/tournament/selectNbT", name="selectNbT")
     */
    public function selectNbT(Request $request): Response
    {
        $nbJoueurs = $request->request->getInt('nbJoueurs');
        if ($nbJoueurs){
            return $this->redirectToRoute('tournament_players',[
                'nbJoueurs' => $nbJoueurs,
                ]);
        }
        return $this->render('tournament/select_nb_players.html.twig', [
            'controller_name' => 'TournamentController',
        ]);
    }
}
