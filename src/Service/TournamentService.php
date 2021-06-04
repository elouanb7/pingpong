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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TournamentService extends AbstractController
{
    private TournamentPlayersRepository $tournamentPlayersRepo;
    private TournamentRepository $tournamentRepo;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;
    private EntityManagerInterface $manager;

    public function __construct(GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, EntityManagerInterface $manager, TournamentPlayersRepository $tournamentPlayersRepo, TournamentRepository $tournamentRepo)
    {
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

        for ($i = 0; $i < (count($tournamentPlayers) / 2); $i++) {
            $game = new Game(); // je crée 1 game
            $game->setTournament($this->tournamentRepo->findOneBy(['id' => $tournament])); // Je récup mon tournois
            $game->setIsTournament(true);
            $game->setIsGoldenRacket(false);
            $game->setGoldenRacket(null);
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

            array_splice($players, 0, count($players) - 2);
        }
        $rounds = $rounds - 1;
        $this->addflash(
            'success',
            "Les matchs du tournois on bien été générés !"
        );
        return $rounds;
    }

    /**
     * @param $tournament
     * @param $round
     * @return array
     */
    public function doTournamentRound($tournament, $round): array
    {
        $tournamentPlayers = $this->tournamentPlayersRepo->findBy(['tournament' => $tournament], ['id' => 'ASC']);
        $previousRoundGames = $this->gameRepo->findBy(['tournament' => $tournament, 'round' => $round]);
        $previousRoundPlayers = [];
        foreach ($previousRoundGames as $previousRoundGame) {
            $previousRoundJouers = $previousRoundGame->getJouers();
            foreach ($previousRoundJouers as $previousRoundJouer) {
                $previousRoundPlayer = $previousRoundJouer->getIsWinner();
                if ($previousRoundPlayer == true){
                    $previousRoundPlayer = $previousRoundJouer->getPlayer()->getId();
                    $previousRoundPlayer = $this->playerRepo->findOneBy(['id' => $previousRoundPlayer]);
                    array_push($previousRoundPlayers, $previousRoundPlayer);
                }
            }
        }
return $previousRoundPlayers;
        /*
         $players = [];
         foreach ($tournamentPlayers as $tournamentPlayer) {
             $playerId = $tournamentPlayer->getPlayer()->getId();
             $player = $this->playerRepo->findOneBy(['id' => $playerId]);
             array_push($players, $player);
         }

         for ($i = 0; $i < (count($tournamentPlayers) / 2); $i++) {
             $game = new Game(); // je crée 1 game
             $game->setTournament($this->tournamentRepo->findOneBy(['id' => $tournament])); // Je récup mon tournois
             $game->setIsTournament(true);
             $game->setIsGoldenRacket(false);
             $game->setGoldenRacket(null);
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

             array_splice($players, 0, count($players)-2);
         }
         $rounds=$rounds-1;
         $this->addflash(
             'success',
             "Les matchs du tournois on bien été générés !"
         );*/

    }
}