<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Jouer;
use App\Entity\Player;
use App\Form\JouerType;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JouerController extends AbstractController
{
    private EntityManagerInterface $manager;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;

    public function __construct(EntityManagerInterface $manager, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo)
    {
        $this->manager = $manager;
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
    }

    /**
     * @Route("/game/newGame/players", name="choosePlayers")
     * @param Request $request
     * @return Response
     */
    public function choosePlayers(Request $request): Response
    {

        $form = $this->createForm(JouerType::class, null);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $playersDatas = $form->getData();
            $countPlayersDatas = count($playersDatas) + 1;
            for ($i = 1; $i < $countPlayersDatas ; $i++) {
                for ($j = $i + 1; $j < $countPlayersDatas; $j++) {
                    if (($playersDatas["player" . $i]->getId()) == ($playersDatas["player" . $j]->getId())) {
                        $this->addflash(
                            'danger',
                            "Un même joueur est sélectionné au moins deux fois !"
                        );
                        return $this->render('jouer/select_player.html.twig', [
                            'form' => $form->createView(),
                        ]);
                    }
                }
            }
            $game = new Game(); // je crée 1 game
            $game->setTournament(null); // Je récup mon tournois
            $game->setIsTournament(false);
            $game->setIsGoldenRacket(false);
            $game->setGoldenRacket(null);
            $game->setDay(null);
            $game->setRound(null);
            foreach ($playersDatas as $playerData){
                $jouer = new Jouer();
                $jouer->setPlayer($playerData);
                $jouer->setGame($game);
                $this->manager->persist($jouer);
            }
            $this->manager->persist($game);
            $this->manager->flush();

            $this->addflash(
                'success',
                "Les joueurs ont bien été enregistrés !"
            );
            return $this->redirectToRoute('newGame', [
                'id' => $game->getId(),
            ]);
        }
        return $this->render('jouer/select_player.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
