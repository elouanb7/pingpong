<?php

namespace App\Service;


use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class GameService extends AbstractController
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

    /**
     * @param $game
     * @return bool
     */
    public function gameInEleven($game): bool
    {
        $jouers = $this->jouerRepo->findBy(['game' => $game->getId()], ['id' => 'ASC']);
        $player1 = $this->playerRepo->findOneBy(['id' => $jouers[0]->getPlayer()]);
        $player2 = $this->playerRepo->findOneBy(['id' => $jouers[1]->getPlayer()]);
        $scoreP1 = $game->getScoreP1();
        $scoreP2 = $game->getScoreP2();

        if ($scoreP1 == $scoreP2) {
            $this->addflash(
                'danger',
                "Les scores de " . $player1->getFullName() . " et " . $player1->getFullName() . " sont identiques, jouez encore un peu, c'est bientôt fini..."
            );
            return false;
        }
        if ($scoreP1 > $scoreP2) {
            if ($scoreP1 < 11) {
                $this->addflash(
                    'danger',
                    "Le score de " . $player1->getFullName() . " est inférieur à 11"
                );
                return false;
            }
            if (($scoreP1 == 11 && $scoreP2 < 10) || ($scoreP1 >= 11 && ($scoreP1 - 2) == $scoreP2)) {
                $jouers[0]->setIsWinner(true)
                    ->setScore($scoreP1);
                $jouers[1]->setIsWinner(false)
                    ->setScore($scoreP2);
                $this->manager->persist($jouers[0]);
                $this->manager->persist($jouers[1]);
                $this->manager->flush();
                return true;
            }
            if ($scoreP1 >= 11 && ($scoreP1 - 2) != $scoreP2) {
                $this->addflash(
                    'danger',
                    "Le score de " . $player1->getFullName() . " ne respecte pas les 2 points d'écart."
                );
                return false;
            }
        } else {
            if ($scoreP2 < 11) {
                $this->addflash(
                    'danger',
                    "Le score de " . $player2->getFullName() . " est inférieur à 11."
                );
                return false;
            }
            if (($scoreP2 == 11 && $scoreP1 < 10) || ($scoreP2 >= 11 && ($scoreP2 - 2) == $scoreP1)) {
                $jouers[0]->setIsWinner(false)
                    ->setScore($scoreP1);
                $jouers[1]->setIsWinner(true)
                    ->setScore($scoreP2);
                $this->manager->persist($jouers[0]);
                $this->manager->persist($jouers[1]);
                $this->manager->flush();
                return true;
            }
            if ($scoreP2 >= 11 && ($scoreP2 - 2) != $scoreP1) {
                $this->addflash(
                    'danger',
                    "Le score de " . $player2->getFullName() . " ne respecte pas les 2 points d'écart."
                );
                return false;
            }
        }
        return false;
    }
}