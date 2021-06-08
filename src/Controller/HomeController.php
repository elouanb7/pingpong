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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{


    // Constructeur
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
     * @Route("/", name="home")
     *
     */
    public function index(): Response
    {
        $this->session->set('goldenRacketId', null);
        $this->session->set('tournamentId', null);
        $games = $this->gameRepo->findBy(['isTournament' => false, 'isGoldenRacket' => false],['playedAt' => 'DESC'], 6);
        $allGames = $this->gameRepo->findBy(['isTournament' => false, 'isGoldenRacket' => false],['playedAt' => 'DESC']);
        $tournaments = $this->tournamentRepo->findBy([],['createdAt' => 'DESC'], 6);
        $allTournaments = $this->tournamentRepo->findBy([],['createdAt' => 'DESC']);
        $goldenRackets = $this->goldenRacketRepo->findBy([],['createdAt' => 'DESC'], 6);
        $allGoldenRackets = $this->goldenRacketRepo->findBy([],['createdAt' => 'DESC']);
        $jouers = $this->jouerRepo->findAll();

        $player = $this->playerRepo->findOneBy(['id' => $this->getUser()]);
        if ($this->getUser()){
           $this->statsService->matchStats($player);
            return $this->render('home/index.html.twig', [
                'games' => $games,
                'allGames' => $allGames,
                'tournaments' => $tournaments,
                'allTournaments' => $allTournaments,
                'goldenRackets' => $goldenRackets,
                'allGoldenRackets' => $allGoldenRackets,
                'jouers' => $jouers,
                'player' => $player,
            ]);
        }

        return $this->render('home/index.html.twig', [
            'games' => $games,
            'allGames' => $allGames,
            'tournaments' => $tournaments,
            'allTournaments' => $allTournaments,
            'goldenRackets' => $goldenRackets,
            'allGoldenRackets' => $allGoldenRackets,
            'jouers' => $jouers,
        ]);
    }
}



