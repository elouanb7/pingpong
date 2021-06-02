<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Tournament;
use App\Entity\TournamentPlayers;
use App\Form\JouerType;
use App\Form\TournamentPlayersType;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TournamentPlayersController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo)
    {
        $this->manager = $manager;
        $this->playerRepo = $playerRepo;
    }

    /**
     * @Route("/tournament/selectPlayer/{nbJoueurs}", name="tournament_players")
     */
    public function index($nbJoueurs, Request $request): Response
    {
        $tournament = new Tournament();
        for ($i= 1 ; $i<$nbJoueurs ; $i++){
            $tournamentPlayer = new TournamentPlayers();
            $form = $this->createForm(TournamentPlayersType::class, $tournamentPlayer);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tournamentPlayer = $form->getData();
                $tournamentPlayer->setTournament($tournament)
                    ->setRank(null);
                $date = new \DateTime('now');
                $tournament->setCreatedAt($date);
                $this->manager->persist($tournament);
                $this->manager->persist($tournamentPlayer);
                $this->manager->flush();
                $this->addflash(
                    'success',
                    "Le joueur " . $i . " à bien été enregistré !"
                );
                $this->render('tournament_players/select_player.html.twig', [
                    'nbJoueur' => $i,
                    'form' => $form->createView(),
                ]);
            }
            $this->render('tournament_players/select_player.html.twig', [
                'controller_name' => 'TournamentPlayersController',
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('home',[]);
    }
}
