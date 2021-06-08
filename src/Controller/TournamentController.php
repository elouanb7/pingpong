<?php

namespace App\Controller;

use App\Service\TournamentService;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentPlayersRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TournamentController extends AbstractController
{
    private TournamentPlayersRepository $tournamentPlayersRepo;
    private TournamentRepository $tournamentRepo;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;
    private EntityManagerInterface $manager;
    private TournamentService $tournamentService;

    public function __construct(GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, EntityManagerInterface $manager, TournamentPlayersRepository $tournamentPlayersRepo, TournamentRepository $tournamentRepo, TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
        $this->tournamentRepo = $tournamentRepo;
        $this->tournamentPlayersRepo = $tournamentPlayersRepo;
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
        $this->manager = $manager;
    }

    /**
     * @Route("/tournament/selectNbT", name="selectNbT")
     */
    public function selectNbT(Request $request): Response
    {

        $nbJoueurs = $request->request->getInt('nbJoueurs');
        if ($nbJoueurs) {
            return $this->redirectToRoute('tournament_players', [
                'nbJoueurs' => $nbJoueurs,
            ]);
        }

        return $this->render('tournament/select_nb_players.html.twig', [
            'controller_name' => 'TournamentController',
        ]);
    }

    /**
     * @Route("/tournament/{id}/grid", name="gridT")
     */
    public function gridOfMatchs(Request $request, $id): Response
    {
        $tournamentPlayers = $this->tournamentPlayersRepo->findBy(['tournament' => $id], ['id' => 'ASC']);
        if (count($tournamentPlayers) == 4) {
            $oldRounds = 2;
        } else if (count($tournamentPlayers) == 8) {
            $oldRounds = 3;
        } else if (count($tournamentPlayers) == 16) {
            $oldRounds = 4;
        } else if (count($tournamentPlayers) == 32) {
            $oldRounds = 5;
        }
        $tournament = $this->tournamentRepo->findOneBy(['id' => $id]);
        if (!$this->gameRepo->findBy(['tournament' => $id])) {
            $newRounds = $this->tournamentService->doTournamentInit($id);
            $tournament->setRound($newRounds);
            $this->manager->persist($tournament);
            $this->manager->flush();
        } else {
            $games = $this->gameRepo->findBy(['tournament' => $id], ['playedAt' => 'ASC']);
            $jouers = $this->jouerRepo->findAll();
            $round = $this->tournamentService->doTournamentRound($id, $tournament->getRound());
            $round = $this->tournamentService->doLeaderboard($round, $id);
            $tournament->setRound($round);
            $this->manager->persist($tournament);
            $this->manager->flush();
            if ($round == 0) {
                $leaderboard = $this->tournamentService->leaderboard($id);
                return $this->render('tournament/grid_of_matchs.html.twig', [
                    'games' => $games,
                    'jouers' => $jouers,
                    'leaderboard' => $leaderboard,
                    'oldRounds' => $oldRounds,
                    'tournament' => $this->tournamentRepo->findOneBy(['id' => $id]),
                ]);
            }
        }
        $games = $this->gameRepo->findBy(['tournament' => $id], ['playedAt' => 'ASC']);
        $jouers = $this->jouerRepo->findAll();

        return $this->render('tournament/grid_of_matchs.html.twig', [
            'games' => $games,
            'jouers' => $jouers,
            'oldRounds' => $oldRounds,
            'leaderboard' => false,
            'tournament' => $this->tournamentRepo->findOneBy(['id' => $id]),
        ]);
    }
}








/* foreach ($games as $game)
        {
            if ($game->getScoreP1()!=null || $game->getScoreP2()!=null){
                   }
            else{
                $nextRound = false;
            }
        }
        if (!$nextRound){
           $newRounds =  $this->tournamentService->doTournament($id, $rounds);
        }*/