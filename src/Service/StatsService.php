<?php

namespace App\Service;


use App\Repository\GameRepository;
use App\Repository\GoldenRacketPlayersRepository;
use App\Repository\GoldenRacketRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentPlayersRepository;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class StatsService extends AbstractController
{

    private GoldenRacketPlayersRepository $goldenRacketPlayersRepo;
    private GoldenRacketRepository $goldenRacketRepo;
    private GameRepository $gameRepo;
    private JouerRepository $jouerRepo;
    private PlayerRepository $playerRepo;
    private TournamentRepository $tournamentRepo;
    private TournamentPlayersRepository $tournamentPlayersRepo;
    private SessionInterface $session;
    private EntityManagerInterface $manager;


    public function __construct(TournamentPlayersRepository $tournamentPlayersRepo, EntityManagerInterface $manager, SessionInterface $session, GoldenRacketPlayersRepository $goldenRacketPlayersRepo, GoldenRacketRepository $goldenRacketRepo, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, TournamentRepository $tournamentRepo)
    {
        $this->gameRepo = $gameRepo;
        $this->tournamentRepo = $tournamentRepo;
        $this->tournamentPlayersRepo = $tournamentPlayersRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
        $this->goldenRacketRepo = $goldenRacketRepo;
        $this->goldenRacketPlayersRepo = $goldenRacketPlayersRepo;
        $this->session = $session;
        $this->manager = $manager;
    }

    public function matchsStats($player)
    {
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
            $player->setMatchAveragePointsOf11(floatval($pointsAverageOfEleven));
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


    public function tournamentStats($player)
    {
        $pands = $this->jouerRepo->findBy(['player' => $player->getId()]);
        $played = [];
        $scores = 0;

        foreach ($pands as $jouers){
            if ($jouers->getScore()){
                array_push($played, $jouers);
                $scores = $scores + $jouers->getScore();
            }
        }

        $tournamentsPlayed = $this->tournamentPlayersRepo->findBy(['player' => $player->getId()]);
        $tournamentWon = $this->tournamentPlayersRepo->findBy(['player' => $player->getId(), 'rank' => 1]);
        $ranks = [];
        $rankAverage = 0;
        foreach ($tournamentsPlayed as $tournamentPlayed){
            $rank = $tournamentPlayed->getRank();
            array_push($ranks,$rank);
            $rankAverage = $rankAverage + $rank;
        }
        if (count($ranks)!=null){
            $tournamentAveragePlacement = $rankAverage/(count($ranks));
            $player->setTournamentAveragePlacement($tournamentAveragePlacement);
        }


        if(!empty($played) || $scores!=0){
            $player->setTournamentPlayed(count($tournamentsPlayed));
            $player->setTournamentWon(count($tournamentWon));

        }

        $this->manager->persist($player);
        $this->manager->flush();
    }

    public function goldenRacketStats($player)
    {
        $pands = $this->jouerRepo->findBy(['player' => $player->getId()]);
        $played = [];
        $scores = 0;

        foreach ($pands as $jouers){
            if ($jouers->getScore()){
                array_push($played, $jouers);
                $scores = $scores + $jouers->getScore();
            }
        }

        $goldenRacketsPlayed = $this->goldenRacketPlayersRepo->findBy(['player' => $player->getId()]);
        $goldenRacketWon = $this->goldenRacketPlayersRepo->findBy(['player' => $player->getId(), 'rank' => 1]);
        $ranks = [];
        $rankAverage = 0;
        foreach ($goldenRacketsPlayed as $goldenRacketPlayed){
            $rank = $goldenRacketPlayed->getRank();
            array_push($ranks,$rank);
            $rankAverage = $rankAverage + $rank;
        }
        if (count($ranks)!=null){
            $goldenRacketAveragePlacement = $rankAverage/(count($ranks));
            $player->setGoldenRacketAveragePlacement($goldenRacketAveragePlacement);
        }


        if(!empty($played) || $scores!=0){
            $player->setGoldenRacketPlayed(count($goldenRacketsPlayed));
            $player->setGoldenRacketWon(count($goldenRacketWon));

        }

        $this->manager->persist($player);
        $this->manager->flush();
    }

}