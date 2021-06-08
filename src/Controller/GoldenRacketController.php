<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\GoldenRacketPlayersRepository;
use App\Repository\GoldenRacketRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Service\GoldenRacketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GoldenRacketController extends AbstractController
{
    private SessionInterface $session;
    private GoldenRacketPlayersRepository $goldenRacketPlayersRepo;
    private GoldenRacketRepository $goldenRacketRepo;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;
    private EntityManagerInterface $manager;
    private GoldenRacketService $goldenRacketService;

    public function __construct(GoldenRacketService $goldenRacketService, SessionInterface $session, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, EntityManagerInterface $manager, GoldenRacketPlayersRepository $goldenRacketPlayersRepo, GoldenRacketRepository $goldenRacketRepo)
    {
        $this->goldenRacketRepo = $goldenRacketRepo;
        $this->goldenRacketPlayersRepo = $goldenRacketPlayersRepo;
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
        $this->manager = $manager;
        $this->session = $session;
        $this->goldenRacketService = $goldenRacketService;
    }


    /**
     * @Route("/goldenRacket/selectNbG", name="selectNbG")
     */
    public function selectNbG(Request $request): Response
    {

        $nbJoueurs = $request->request->getInt('nbJoueurs');
        if ($nbJoueurs) {
            $this->session->set('nbJoueurs', $nbJoueurs);
            return $this->redirectToRoute('golden_racket_players', [
            ]);
        }
        return $this->render('golden_racket/select_nb_players.html.twig', [
        ]);
    }

    /**
     * @Route("/goldenRacket/{id}/grid", name="gridG")
     */
    public function gridOfMatchs(Request $request, $id): Response
    {
        $games = $this->gameRepo->findBy(['goldenRacket' => $id], ['playedAt' => 'ASC']);
        $jouers = $this->jouerRepo->findAll();
        $goldenRacket = $this->goldenRacketRepo->findOneBy(['id' => $id]);
        $days = $goldenRacket->getDay();
        /* $this->goldenRacketService->doGoldenRacketDay($id);*/
        return $this->render('golden_racket/grid_of_matchs.html.twig', [
            'goldenRacket' => $goldenRacket,
            'games' => $games,
            'jouers' => $jouers,
            'days' => $days
        ]);
    }

    /**
     * @Route("/goldenRacket/{id}/newDay", name="newDay")
     */
    public function newDay($id): Response
    {
        $this->goldenRacketService->doGoldenRacketDay($id);
        return $this->redirectToRoute('gridG', [
            'id' => $id,
        ]);
    }
}

