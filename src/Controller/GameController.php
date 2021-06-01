<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Jouer;
use App\Entity\Player;
use App\Form\GameType;
use App\Form\JouerType;
use App\Service\GameService;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class GameController extends AbstractController
{
    private GameService $gameService;
    private EntityManagerInterface $manager;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;

    public function __construct(EntityManagerInterface $manager, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, GameService $gameService)
    {
        $this->gameService = $gameService;
        $this->manager = $manager;
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
    }


    /**
     * @Route("/game/liste", name="games")
     */
    public function games(): Response
    {
        return $this->render('game/games.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }

    /**
     * @Route("/game/{id}", name="newGame")
     * @param Request $request
     * @param $id
     * @return Response
     */
     public function newGame(Request $request, $id): Response
     {

         $game = $this->gameRepo->findOneBy(['id' => $id]);
         $jouers = $this->jouerRepo->findBy(['game' => $game->getId()],['id' => 'ASC']);
         $player1 = $this->playerRepo->findOneBy(['id' => $jouers[0]->getPlayer()]);
         $player2 = $this->playerRepo->findOneBy(['id' => $jouers[1]->getPlayer()]);
         $form = $this->createForm(GameType::class, $game);
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
             $game = $form->getData();
             $isScoreValid = $this->gameService->gameInEleven($game);
             if (!$isScoreValid){
                 return $this->render('game/newGame.html.twig', [
                     'form' => $form->createView(),
                     'player1' => $player1,
                     'player2' => $player2,
                 ]);
             }
             $game->setIsTournament(false);
             $game->setIsGoldenRacket(false);
             $game->setRound(null);
             $game->setGoldenRacket(null);
             $game->setTournament(null);
             $this->manager->persist($game);
             $this->manager->flush();
             $this->addflash(
                 'success',
                 "Le score à bien été enregistré"
             );
             return $this->redirectToRoute('home', [
                 'id' => $game->getId()
             ]);
         }
         return $this->render('game/newGame.html.twig', [
             'form' => $form->createView(),
             'player1' => $player1,
             'player2' => $player2,
         ]);
}
}
