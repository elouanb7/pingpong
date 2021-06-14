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

class PlayerController extends AbstractController
{

    private GoldenRacketPlayersRepository $goldenRacketPlayersRepo;
    private GoldenRacketRepository $goldenRacketRepo;
    private GameRepository $gameRepo;
    private JouerRepository $jouerRepo;
    private StatsService $statsService;
    private PlayerRepository $playerRepo;
    private TournamentRepository $tournamentRepo;
    private TournamentPlayersRepository $tournamentPlayersRepo;
    private SessionInterface $session;


    public function __construct(TournamentPlayersRepository $tournamentPlayersRepo, SessionInterface $session, GoldenRacketPlayersRepository $goldenRacketPlayersRepo, GoldenRacketRepository $goldenRacketRepo, GameRepository $gameRepo, JouerRepository $jouerRepo, StatsService $statsService, PlayerRepository $playerRepo, TournamentRepository $tournamentRepo)
    {
        $this->gameRepo = $gameRepo;
        $this->tournamentRepo = $tournamentRepo;
        $this->tournamentPlayersRepo = $tournamentPlayersRepo;
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
    {   $player = $this->playerRepo->findOneBy(['id' => $id]);
        $games = [];
        $jouersP = $this->jouerRepo->findBy(['player' => $id],['playedAt' => 'DESC'], );
        $jouers = $this->jouerRepo->findAll();
        $tournaments = [];
        $tournamentPlayers = $this->tournamentPlayersRepo->findBy(['player' => $id]);
        $goldenRackets = [];
        $goldenRacketPlayers = $this->goldenRacketPlayersRepo->findBy(['player' => $id]);
        $validJouers = [];
        foreach ($jouersP as $jouerP){
            if (!$jouerP->getScore()==null){
                array_push($validJouers, $jouerP);
            }
        }
        $jouersP = $validJouers;
        foreach ($jouersP as $jouerP){
            $game = $jouerP->getGame()->getId();
            $game = $this->gameRepo->findOneBy(['id' => $game]);
            if (($game->getIsTournament()==true)){}
            elseif ($game->getIsGoldenRacket()==true){}
            else{
                array_push($games,$game);
            }
        }
        foreach ($tournamentPlayers as $tournamentPlayer){
            $tournament = $this->tournamentRepo->findOneBy(['id' => $tournamentPlayer->getTournament()]);
            array_push($tournaments, $tournament);
        }
        foreach ($goldenRacketPlayers as $goldenRacketPlayer){
            $goldenRacket = $this->goldenRacketRepo->findOneBy(['id' => $goldenRacketPlayer->getGoldenRacket()]);
            array_push($goldenRackets, $goldenRacket);
        }


        return $this->render('player/profile.html.twig', [
            'player' => $player,
            'games' => $games,
            'jouers' => $jouers,
            'jouersP' => $jouersP,
            'tournaments' => $tournaments,
            'tournamentPlayers' => $tournamentPlayers,
            'goldenRackets' => $goldenRackets,
            'goldenRacketPlayers' => $goldenRacketPlayers,
        ]);
    }
}
