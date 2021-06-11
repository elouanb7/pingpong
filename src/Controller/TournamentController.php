<?php

namespace App\Controller;

use App\Service\TournamentService;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentPlayersRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TournamentController extends AbstractController
{
    private SessionInterface $session;
    private TournamentPlayersRepository $tournamentPlayersRepo;
    private TournamentRepository $tournamentRepo;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;
    private EntityManagerInterface $manager;
    private TournamentService $tournamentService;

    public function __construct(SessionInterface $session, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, EntityManagerInterface $manager, TournamentPlayersRepository $tournamentPlayersRepo, TournamentRepository $tournamentRepo, TournamentService $tournamentService)
    {
        $this->tournamentService = $tournamentService;
        $this->tournamentRepo = $tournamentRepo;
        $this->tournamentPlayersRepo = $tournamentPlayersRepo;
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
        $this->manager = $manager;
        $this->session = $session;
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
        $this->session->set('tournamentId', $id);
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
            $players = $this->playerRepo->findAll();
            foreach ($players as $player){
                $this->tournamentService->updateStats($player->getId(), $id);
            }
            $games = $this->gameRepo->findBy(['tournament' => $id], ['playedAt' => 'ASC']);
            $nextGames = $this->gameRepo->findBy(['tournament' => $id, 'scoreP1' => null, 'scoreP2' => null], ['playedAt' => 'ASC'], 1);
            $jouers = $this->jouerRepo->findAll();
            $round = $this->tournamentService->doTournamentRound($id, $tournament->getRound());
            $round = $this->tournamentService->doLeaderboard($round, $id);
            $tournament->setRound($round);
            $this->manager->persist($tournament);
            $this->manager->flush();
            if ($round == 0) {
                $this->tournamentService->leaderboard($id);
                $leaderboardP = $this->tournamentPlayersRepo->findBy(['tournament' => $id], ['rank' => 'ASC']);
                $leaderboard = $this->tournamentPlayersRepo->findBy(['tournament' => $id], ['rank' => 'ASC']);
                $playersl = [];
                foreach ($leaderboard as $playerl){
                    $playerl = $playerl->getPlayer()->getId();
                    $playerl = $this->playerRepo->findOneBy(['id' => $playerl]);
                    array_push($playersl,$playerl);
                }
                $leaderboard = $playersl;
                return $this->render('tournament/grid_of_matchs.html.twig', [
                    'games' => $games,
                    'nextGames' => $nextGames,
                    'jouers' => $jouers,
                    'leaderboard' => $leaderboard,
                    'leaderboardP' => $leaderboardP,
                    'oldRounds' => $oldRounds,
                    'tournament' => $this->tournamentRepo->findOneBy(['id' => $id]),
                ]);
            }
        }
        $nextGames = $this->gameRepo->findBy(['tournament' => $id, 'scoreP1' => null, 'scoreP2' => null], ['playedAt' => 'ASC'], 1);
        $games = $this->gameRepo->findBy(['tournament' => $id], ['playedAt' => 'ASC']);
        $jouers = $this->jouerRepo->findAll();

        return $this->render('tournament/grid_of_matchs.html.twig', [
            'games' => $games,
            'nextGames' => $nextGames,
            'jouers' => $jouers,
            'oldRounds' => $oldRounds,
            'leaderboard' => false,
            'tournament' => $this->tournamentRepo->findOneBy(['id' => $id]),
        ]);
    }

    /**
     * @Route("/tournament/liste", name="tournaments")
     */
    public function tournaments(PaginatorInterface $paginator, Request $request): Response
    {

        $tournaments = $this->tournamentRepo->findBy([],['createdAt' => 'DESC']);
        $pagination = $paginator->paginate(
            $tournaments,
            $request->query->getInt('page', 1),
            8
        );
        $pagination->setTemplate('ressources/twitter_bootstrap_v4_pagination.html.twig');
        $pagination->setCustomParameters([
            'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination and foundation_v6_pagination)
            'style' => 'bottom',
            'span_class' => 'whatever',
        ]);
        return $this->render('tournament/tournaments.html.twig', [
            'tournaments' => $tournaments,
            'pagination' => $pagination,
        ]);
    }

}
