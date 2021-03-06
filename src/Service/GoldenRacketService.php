<?php

namespace App\Service;


use App\Entity\Game;
use App\Entity\Jouer;
use App\Entity\Tournament;
use App\Repository\GameRepository;
use App\Repository\GoldenRacketPlayersRepository;
use App\Repository\GoldenRacketRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentPlayersRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class GoldenRacketService extends AbstractController
{
    private GoldenRacketPlayersRepository $goldenRacketPlayersRepo;
    private GoldenRacketRepository $goldenRacketRepo;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;
    private EntityManagerInterface $manager;

    public function __construct(GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, EntityManagerInterface $manager, GoldenRacketPlayersRepository $goldenRacketPlayersRepo, GoldenRacketRepository $goldenRacketRepo)
    {
        $this->goldenRacketRepo = $goldenRacketRepo;
        $this->goldenRacketPlayersRepo = $goldenRacketPlayersRepo;
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
        $this->manager = $manager;
    }

    /**
     * @param $goldenRacket
     * @return bool
     */

    public function doGoldenRacketDay($goldenRacket): bool
    {
        $goldenRacketPlayers = $this->goldenRacketPlayersRepo->findBy(['goldenRacket' => $goldenRacket], ['id' => 'ASC']);
        $goldenRacket = $this->goldenRacketRepo->findOneBy(['id' => $goldenRacket]);
        $goldenRacket->setDay(($goldenRacket->getDay()+1));

        $players = [];
        foreach ($goldenRacketPlayers as $goldenRacketPlayer) {
            $playerId = $goldenRacketPlayer->getPlayer()->getId();
            $player = $this->playerRepo->findOneBy(['id' => $playerId]);
            array_push($players, $player);
        }

        for($i = 0; $i < count($goldenRacketPlayers); $i++){
            for ($j = $i+1 ; $j <= count($goldenRacketPlayers) - 1; $j++){
                $game = new Game(); // je cr??e 1 game
                $game->setGoldenRacket($this->goldenRacketRepo->findOneBy(['id' => $goldenRacket->getId()])); // Je r??cup mon tournois
                $game->setDay($goldenRacket->getDay());
                $game->setIsGoldenRacket(true);
                $game->setIsTournament(false);
                $game->setTournament(null);
                $game->setRound(null);
                $jouer = new Jouer(); // Je cr??e une participation
                $jouer->setPlayer($players[$i]); // Je d??fini le joueur qui participe
                $jouer->setGame($game); // Je d??fini dans quelle game
                $jouer2 = new Jouer();
                $jouer2->setPlayer($players[$j]);
                $jouer2->setGame($game);

                $this->manager->persist($jouer);
                $this->manager->persist($jouer2);
                $this->manager->persist($game);
                $this->manager->flush();
            }
        }
        $this->manager->persist($goldenRacket);
        $this->manager->flush();
        $this->addflash(
            'success',
            "Les matchs du tournois on bien ??t?? g??n??r??s !"
        );
        return false;
    }

    /**
     * @param int $player
     * @param int $goldenRacket
     * @return bool
     */

    public function updateStats(int $player, int $goldenRacket): bool
    {
        $goldenRacketPlayer = $this->goldenRacketPlayersRepo->findOneBy(['player' => $player, 'goldenRacket' => $goldenRacket]);
        $goldenRacketJouers = [];
        $validJouers = [];
        $jouers = $this->jouerRepo->findBy(['player' => $player]);
        foreach ($jouers as $jouer) {
            if ($jouer->getScore() ||$jouer->getScore()==0 && $jouer->getScore()!==null) {
                array_push($validJouers, $jouer);
            }
        }
        $jouers = $validJouers;
        $games = $this->gameRepo->findBy(['goldenRacket' => $goldenRacket]);
        foreach ($jouers as $jouer) {
            foreach ($games as $game) {
                if ($jouer->getGame()->getId() == $game->getId()) {
                    array_push($goldenRacketJouers, $jouer);
                }
            }
        }
        $goldenRacketScore = 0;
        $goldenRacketMatchW = 0;
        if (count($goldenRacketJouers) != null) {
            foreach ($goldenRacketJouers as $goldenRacketJouer) {
                $goldenRacketScore = $goldenRacketScore + $goldenRacketJouer->getScore();
                if ($goldenRacketJouer->getIsWinner() == true){
                    $goldenRacketMatchW++;
                }
            }
            $goldenRacketMatchRatio = $goldenRacketMatchW / (count($goldenRacketJouers));
            $goldenRacketPointsRatio = $goldenRacketScore / (count($goldenRacketJouers));
            $goldenRacketPlayer->setRatioWL($goldenRacketMatchRatio);
            $goldenRacketPlayer->setPointsAverage($goldenRacketPointsRatio);
            $goldenRacketPlayer->setNbGames(count($goldenRacketJouers));
            $this->manager->persist($goldenRacketPlayer);
            $this->manager->flush();
        } else {
            return false;
        }
        return true;
    }

    /**
     * @param int $goldenRacket
     * @return bool
     */

    public function leaderboard(int $goldenRacket): bool
    {
        //TODO : CHANGER LE MODE DE CLASSEMENT "RAQUETTE OR" ET CLASSER PAR VICTOIRES AVANT DE CLASSER PAR RATIO

        // Tri en fonction du ratio de pts

        $goldenRacketPlayers = $this->goldenRacketPlayersRepo->findBy(['goldenRacket' => $goldenRacket], ['ratioWL' => 'ASC']);
        $countGoldenRacketPlayers = count($goldenRacketPlayers);
        foreach ($goldenRacketPlayers as $goldenRacketPlayer) {
            if ($goldenRacketPlayer->getNbGames()){
                $goldenRacketPlayer->setRank($countGoldenRacketPlayers);
                $this->manager->persist($goldenRacketPlayer);
                $this->manager->flush();
            }
            else{
                $goldenRacketPlayer->setRank(999);
                $this->manager->persist($goldenRacketPlayer);
                $this->manager->flush();
            }
            $countGoldenRacketPlayers--;
        }

        return true;
    }
}