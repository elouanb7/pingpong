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
        $played = $this->jouerRepo->findBy(['player' => $player, 'score' => !null ]);
        $won = $this->jouerRepo->findBy(['player' => $player, 'isWinner' => true, 'score' => !null ]);
        $lost = $this->jouerRepo->findBy(['player' => $player, 'isWinner' => false, 'score' => !null ]);
        if (!$won && !$lost){
            $ratio = count($won)/count($won)+count($lost);
        }
        $player->setMatchPlayed(count($played));
        $player->setMatchWon(count($won));
        $player->setMatchLost(count($lost));
        $player->setMatchRatio($ratio);
        $this->manager->persist($player);
        $this->manager->flush();
    }

}