<?php

namespace App\Service;


use App\Entity\Game;
use App\Entity\Jouer;
use App\Entity\Tournament;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentPlayersRepository;
use App\Repository\TournamentRepository;
use App\Service\GeneralService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TournamentService extends AbstractController
{
    private GeneralService $generalService;
    private TournamentPlayersRepository $tournamentPlayersRepo;
    private TournamentRepository $tournamentRepo;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;
    private EntityManagerInterface $manager;

    public function __construct(GeneralService $generalService, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, EntityManagerInterface $manager, TournamentPlayersRepository $tournamentPlayersRepo, TournamentRepository $tournamentRepo)
    {
        $this->generalService = $generalService;
        $this->tournamentRepo = $tournamentRepo;
        $this->tournamentPlayersRepo = $tournamentPlayersRepo;
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
        $this->manager = $manager;
    }

    /**
     * @param $tournament
     * @return int
     */

    public function doTournamentInit($tournament): int
    {
        $tournamentPlayers = $this->tournamentPlayersRepo->findBy(['tournament' => $tournament], ['id' => 'ASC']);
        if (count($tournamentPlayers) == 4) {
            $rounds = 2;
        } else if (count($tournamentPlayers) == 8) {
            $rounds = 3;
        } else if (count($tournamentPlayers) == 16) {
            $rounds = 4;
        } else if (count($tournamentPlayers) == 32) {
            $rounds = 5;
        }
        $players = [];
        foreach ($tournamentPlayers as $tournamentPlayer) {
            $playerId = $tournamentPlayer->getPlayer()->getId();
            $player = $this->playerRepo->findOneBy(['id' => $playerId]);
            array_push($players, $player);
        }
        $countTournamentPlayers = (count($tournamentPlayers) / 2);
        for ($i = 0; $i < ($countTournamentPlayers); $i++) {
            $game = new Game(); // je crée 1 game
            $game->setTournament($this->tournamentRepo->findOneBy(['id' => $tournament])); // Je récup mon tournois
            $game->setIsTournament(true);
            $game->setIsGoldenRacket(false);
            $game->setGoldenRacket(null);
            $game->setDay(null);
            $game->setRound($rounds);
            $jouer = new Jouer(); // Je crée une participation
            $jouer->setPlayer($players[0]); // Je défini le joueur qui participe
            $jouer->setGame($game); // Je défini dans quelle game
            $jouer2 = new Jouer();
            $jouer2->setPlayer($players[1]);
            $jouer2->setGame($game);

            $this->manager->persist($jouer);
            $this->manager->persist($jouer2);
            $this->manager->persist($game);
            $this->manager->flush();

            array_splice($players, 0, 2);
        }
        $this->addflash(
            'success',
            "Les matchs du tournois on bien été générés !"
        );
        return $rounds;
    }

    /**
     * @param $tournament
     * @param $round
     * @return int
     */
    public function doTournamentRound($tournament, $round): int
    {

        $tournamentPlayers = $this->tournamentPlayersRepo->findBy(['tournament' => $tournament], ['id' => 'ASC']);
        $previousRoundGames = $this->gameRepo->findBy(['tournament' => $tournament, 'round' => $round]);
        $previousRoundPlayers = [];
        foreach ($previousRoundGames as $previousRoundGame) {
            $previousRoundJouers = $previousRoundGame->getJouers();
            foreach ($previousRoundJouers as $previousRoundJouer) {
                $previousRoundPlayer = $previousRoundJouer->getIsWinner();
                if ($previousRoundPlayer == true) {
                    $previousRoundPlayer = $previousRoundJouer->getPlayer()->getId();
                    $previousRoundPlayer = $this->playerRepo->findOneBy(['id' => $previousRoundPlayer]);
                    array_push($previousRoundPlayers, $previousRoundPlayer);
                }
            }
        }

        $nextRound = true;
        foreach ($previousRoundGames as $previousRoundGame) {
            if ($previousRoundGame->getScoreP1() == null || $previousRoundGame->getScoreP2() == null) {
                $nextRound = false;
            }
        }

        if ($round >= 2 && $nextRound == true) {
            $round = $round - 1;
            $countPreviousRoundPlayers = count($previousRoundPlayers) / 2;
            for ($i = 0; $i < $countPreviousRoundPlayers; $i++) {
                $game = new Game(); // je crée 1 game
                $game->setTournament($this->tournamentRepo->findOneBy(['id' => $tournament])); // Je récup mon tournois
                $game->setIsTournament(true);
                $game->setIsGoldenRacket(false);
                $game->setGoldenRacket(null);
                $game->setDay(null);
                $game->setRound($round);
                $jouer = new Jouer(); // Je crée une participation
                $jouer->setPlayer($previousRoundPlayers[0]); // Je défini le joueur qui participe
                $jouer->setGame($game); // Je défini dans quelle game
                $jouer2 = new Jouer();
                $jouer2->setPlayer($previousRoundPlayers[1]);
                $jouer2->setGame($game);

                $this->manager->persist($jouer);
                $this->manager->persist($jouer2);
                $this->manager->persist($game);
                $this->manager->flush();

                array_splice($previousRoundPlayers, 0, 2);
            }

            $this->addflash(
                'success',
                "Les matchs suivants du tournois ont bien été générés !"
            );

        }

        return $round;
    }

    /**
     * @param int $round
     * @param int $tournament
     * @return int
     */

    public function doLeaderboard(int $round, int $tournament): int
    {
        $finale = $this->gameRepo->findOneBy(['round' => 1, 'tournament' => $tournament]);
        if ($finale) {
            if ($finale->getScoreP1() != null && $finale->getScoreP2() != null) {
                if ($round == 0) {
                    return $round;
                }
                $tournament = $this->tournamentRepo->findOneBy(['id' => $tournament]);
                $tournament->setFinishedAt(new \DateTime('now'));
                $this->manager->persist($tournament);
                $this->manager->flush();
                return $round - 1;
            } else {
                return $round;
            }
        }
        return $round;
    }

    /**
     * @param int $player
     * @param int $tournament
     * @return bool
     */

    public function updateStats(int $player, int $tournament): bool
    {
        $tournamentPlayer = $this->tournamentPlayersRepo->findOneBy(['player' => $player, 'tournament' => $tournament]);
        $tournamentJouers = [];
        $validJouers = [];
        $jouers = $this->jouerRepo->findBy(['player' => $player]);
        foreach ($jouers as $jouer) {
            if ($jouer->getScore()) {
                array_push($validJouers, $jouer);
            }
        }
        $jouers = $validJouers;
        $games = $this->gameRepo->findBy(['tournament' => $tournament]);
        foreach ($jouers as $jouer) {
            foreach ($games as $game) {
                if ($jouer->getGame()->getId() == $game->getId()) {
                    array_push($tournamentJouers, $jouer);
                }
            }
        }
        $tournamentScore = 0;
        if (count($tournamentJouers) != null) {
            foreach ($tournamentJouers as $tournamentJouer) {
                $tournamentScore = $tournamentScore + $tournamentJouer->getScore();
            }
            $tournamentPointsRatio = $tournamentScore / (count($tournamentJouers));
            $tournamentPlayer->setPointsAverage($tournamentPointsRatio);
            $this->manager->persist($tournamentPlayer);
            $this->manager->flush();
        } else {
            return false;
        }
        return true;
    }

    /**
     * @param int $tournament
     * @return bool
     */

    public function leaderboard(int $tournament): bool
    {
        // Places 1 et 2

        $podium = [];
        $finale = $this->gameRepo->findOneBy(['round' => 1, 'tournament' => $tournament]);
        $gagnant = $this->jouerRepo->findOneBy(['game' => $finale->getId(), 'isWinner' => true]);
        $gagnant = $this->playerRepo->findOneBy(['id' => $gagnant->getPlayer()->getId()]);
        $tournamentPlayerGagnant = $this->tournamentPlayersRepo->findOneBy(['tournament' => $tournament, 'player' => $gagnant->getId()]);
        $tournamentPlayerGagnant->setRank(1);
        $this->manager->persist($tournamentPlayerGagnant);
        array_push($podium, $gagnant);
        $finaliste = $this->jouerRepo->findOneBy(['game' => $finale->getId(), 'isWinner' => false]);
        $finaliste = $this->playerRepo->findOneBy(['id' => $finaliste->getPlayer()->getId()]);
        $tournamentPlayerFinaliste = $this->tournamentPlayersRepo->findOneBy(['tournament' => $tournament, 'player' => $finaliste->getId()]);
        $tournamentPlayerFinaliste->setRank(2);
        $this->manager->persist($tournamentPlayerFinaliste);
        array_push($podium, $finaliste);
        $this->manager->flush();

        // Reste du tableau

        $Perdants = [];
        $loserPlayers = [];
        $allTournamentPlayers = $this->tournamentPlayersRepo->findBy(['tournament' => $tournament]);
        $allTournamentPlayers = count($allTournamentPlayers);
        for ($i = 5; $i > 1; $i--) {
            $games = $this->gameRepo->findBy(['round' => $i, 'tournament' => $tournament]);
            foreach ($games as $game) {
                $perdant = $this->jouerRepo->findOneBy(['game' => $game->getId(), 'isWinner' => false]);
                $perdant = $this->playerRepo->findOneBy(['id' => $perdant->getPlayer()->getId()]);
                array_push($Perdants, $perdant);
            }
            foreach ($Perdants as $perdant) {
                $tournamentPlayer = $this->tournamentPlayersRepo->findOneBy(['player' => $perdant->getId(), 'tournament' => $tournament]);
                $tournamentPlayerRatio = $tournamentPlayer->getPointsAverage();
                $loserPlayers [] = ['id' => $perdant->getId(), 'ratio' => $tournamentPlayerRatio];
                $loserPlayers = $this->generalService->array_sort($loserPlayers, 'ratio', SORT_ASC);
            }
            foreach ($loserPlayers as $loserPlayer) {
                $tournamentPlayer = $this->tournamentPlayersRepo->findOneBy(['player' => $loserPlayer['id'], 'tournament' => $tournament]);
                $tournamentPlayer->setRank($allTournamentPlayers);
                $this->manager->persist($tournamentPlayer);
                $this->manager->flush();
                $allTournamentPlayers--;

            }
            $loserPlayers = [];
            $Perdants = [];
        }


        return true;
    }
}