<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\GoldenRacketPlayersRepository;
use App\Repository\GoldenRacketRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentRepository;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{

    private GoldenRacketPlayersRepository $goldenRacketPlayersRepo;
    private GoldenRacketRepository $goldenRacketRepo;
    private GameRepository $gameRepo;
    private JouerRepository $jouerRepo;
    private StatsService $statsService;
    private PlayerRepository $playerRepo;
    private TournamentRepository $tournamentRepo;
    private SessionInterface $session;


    public function __construct(SessionInterface $session, GoldenRacketPlayersRepository $goldenRacketPlayersRepo, GoldenRacketRepository $goldenRacketRepo, GameRepository $gameRepo, JouerRepository $jouerRepo, StatsService $statsService, PlayerRepository $playerRepo, TournamentRepository $tournamentRepo)
    {
        $this->gameRepo = $gameRepo;
        $this->tournamentRepo = $tournamentRepo;
        $this->jouerRepo = $jouerRepo;
        $this->statsService = $statsService;
        $this->playerRepo = $playerRepo;
        $this->goldenRacketRepo = $goldenRacketRepo;
        $this->goldenRacketPlayersRepo = $goldenRacketPlayersRepo;
        $this->session = $session;
    }

    /**
     * @Route("/profile/{id}", name="profile")
     */
    public function index($id): Response
    {
        $player = $this->playerRepo->findOneBy(['id' => $id]);
        return $this->render('player/profile.html.twig', [
            'player' => $player,
        ]);
    }
}
