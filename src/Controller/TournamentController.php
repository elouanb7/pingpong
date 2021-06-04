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
        if ($nbJoueurs){
            return $this->redirectToRoute('tournament_players',[
                'nbJoueurs' => $nbJoueurs,
            ]);
        }

        return $this->render('tournament/select_nb_players.html.twig', [
            'controller_name' => 'TournamentController',
        ]);
    }

    /**
     * @Route("/tournament/{id}/grid", name="grid")
     */
    public function gridOfMatchs(Request $request, $id): Response
    {

        if (!$this->gameRepo->findOneBy(['tournament' => $id])) {
            $newRounds = $this->tournamentService->doTournamentInit($id);
         $rounds = $newRounds;
        }
        else{
            $rounds = $this->gameRepo->findOneBy(['tournament' => $id], ['playedAt' => 'ASC']);
            $rounds = $rounds->getRound();
        }
        $games = $this->gameRepo->findBy(['tournament' => $id]);
        $jouers = $this->jouerRepo->findAll();
        /*$abc =  $this->tournamentService->doTournamentRound($id,$rounds);*/

        return $this->render('tournament/grid_of_matchs.html.twig', [
            'games' => $games,
            'jouers' => $jouers,
            'rounds' => $rounds,
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