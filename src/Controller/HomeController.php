<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\GoldenRacketPlayersRepository;
use App\Repository\GoldenRacketRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentPlayersRepository;
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
    private TournamentPlayersRepository $tournamentPlayersRepo;


    public function __construct(TournamentPlayersRepository $tournamentPlayersRepo, SessionInterface $session, GoldenRacketPlayersRepository $goldenRacketPlayersRepo, GoldenRacketRepository $goldenRacketRepo, GameRepository $gameRepo, JouerRepository $jouerRepo, StatsService $statsService, PlayerRepository $playerRepo, TournamentRepository $tournamentRepo)
    {
        $this->gameRepo = $gameRepo;
        $this->tournamentPlayersRepo = $tournamentPlayersRepo;
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
        $this->session->set('newDayVar', false);
        $games = $this->gameRepo->findBy(['isTournament' => false, 'isGoldenRacket' => false], ['playedAt' => 'DESC'], 6);
        $allGames = $this->gameRepo->findBy(['isTournament' => false, 'isGoldenRacket' => false], ['playedAt' => 'DESC']);
        $tournaments = $this->tournamentRepo->findBy([], ['createdAt' => 'DESC'], 6);
        $allTournaments = $this->tournamentRepo->findBy([], ['createdAt' => 'DESC']);
        $goldenRackets = $this->goldenRacketRepo->findBy([], ['createdAt' => 'DESC'], 6);
        $allGoldenRackets = $this->goldenRacketRepo->findBy([], ['createdAt' => 'DESC']);
        $jouers = $this->jouerRepo->findAll();
        $players = $this->playerRepo->findAll();
        foreach ($players as $player) {
            $this->statsService->matchsStats($player);
            $this->statsService->tournamentStats($player);
            $this->statsService->goldenRacketStats($player);
        }

        if ($this->getUser()) {
            $playerLogged = $this->playerRepo->findOneBy(['id' => $this->getUser()]);
            $tournamentPlayers = $this->tournamentPlayersRepo->findBy(['player' => $playerLogged->getId()]);
            $goldenRacketPlayers = $this->goldenRacketPlayersRepo->findBy(['player' => $playerLogged->getId()]);
            $response = $this->render('home/index.html.twig', [
                'games' => $games,
                'allGames' => $allGames,
                'tournaments' => $tournaments,
                'tournamentPlayers' => $tournamentPlayers,
                'allTournaments' => $allTournaments,
                'goldenRackets' => $goldenRackets,
                'goldenRacketPlayers' => $goldenRacketPlayers,
                'allGoldenRackets' => $allGoldenRackets,
                'jouers' => $jouers,
                'player' => $playerLogged,
            ]);
            $response->setMaxAge(3600);
            return $response;
        }

        $response = $this->render('home/index.html.twig', [
            'games' => $games,
            'allGames' => $allGames,
            'tournaments' => $tournaments,
            'allTournaments' => $allTournaments,
            'goldenRackets' => $goldenRackets,
            'allGoldenRackets' => $allGoldenRackets,
            'jouers' => $jouers,
        ]);
        $response->setMaxAge(3600);
        return $response;
    }
}



