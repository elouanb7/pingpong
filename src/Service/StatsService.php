<?php

namespace App\Service;


use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class StatsService extends AbstractController
{

    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;
    private EntityManagerInterface $manager;

    public function __construct(GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, EntityManagerInterface $manager)
    {
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
        $this->manager = $manager;
    }

    public function matchStats($player)
    {
        $null = null;
        $pands = $this->jouerRepo->findBy(['player' => $player->getId()]);
        $played = [];
        $scores = 0;

        foreach ($pands as $jouers){
            if ($jouers->getScore()){
                array_push($played, $jouers);
                $scores = $scores + $jouers->getScore();
            }
        }
        if(!empty($played) || $scores!=0){
            $pointsAverageOfEleven = $scores/count($played);
            $player->setMatchPlayed(count($played));
            $player->setMatchAveragePointsOf11($pointsAverageOfEleven);
        }
        $won = $this->jouerRepo->findBy(['player' => $player, 'isWinner' => true]);
        $lost = $this->jouerRepo->findBy(['player' => $player, 'isWinner' => false]);
        if (!empty($won) || !empty($lost)){
            $ratio = count($won)/(count($won)+count($lost));
            $player->setMatchRatio($ratio);
            $player->setMatchWon(count($won));
            $player->setMatchLost(count($lost));
        }
        $this->manager->persist($player);
        $this->manager->flush();
    }

}