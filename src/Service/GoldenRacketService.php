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
                $game = new Game(); // je crée 1 game
                $game->setGoldenRacket($this->goldenRacketRepo->findOneBy(['id' => $goldenRacket->getId()])); // Je récup mon tournois
                $game->setDay($goldenRacket->getDay());
                $game->setIsGoldenRacket(true);
                $game->setIsTournament(false);
                $game->setTournament(null);
                $game->setRound(null);
                $jouer = new Jouer(); // Je crée une participation
                $jouer->setPlayer($players[$i]); // Je défini le joueur qui participe
                $jouer->setGame($game); // Je défini dans quelle game
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
            "Les matchs du tournois on bien été générés !"
        );
        return false;
    }
}