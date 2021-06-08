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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class GameController extends AbstractController
{
    private GameService $gameService;
    private EntityManagerInterface $manager;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;
    private SessionInterface $session;

    public function __construct( SessionInterface $session, EntityManagerInterface $manager, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, GameService $gameService)
    {
        $this->gameService = $gameService;
        $this->manager = $manager;
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
        $this->session = $session;
    }


    /**
     * @Route("/game/liste", name="games")
     */
    public function games(PaginatorInterface $paginator, Request $request): Response
    {
        $games = $this->gameRepo->findBy(['isTournament' => false, 'isGoldenRacket' => false],['playedAt' => 'DESC'], 6);
        $jouers = $this->jouerRepo->findAll();
        $pagination = $paginator->paginate(
            $games,
            $request->query->getInt('page', 1),
            8
        );
        $pagination->setTemplate('ressources/twitter_bootstrap_v4_pagination.html.twig');
        $pagination->setCustomParameters([
            'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination and foundation_v6_pagination)
            'style' => 'bottom',
            'span_class' => 'whatever',
        ]);
        return $this->render('game/games.html.twig', [
            'games' => $games,
            'jouers' => $jouers,
            'pagination' => $pagination,
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
         $goldenRacketId = null;
         if ($this->session->get('goldenRacketId')!=null){
             $goldenRacketId = $this->session->get('goldenRacketId');
         }
         $game = $this->gameRepo->findOneBy(['id' => $id]);
         $jouers = $this->jouerRepo->findBy(['game' => $game->getId()],['id' => 'ASC']);
         $player1 = $this->playerRepo->findOneBy(['id' => $jouers[0]->getPlayer()]);
         $player2 = $this->playerRepo->findOneBy(['id' => $jouers[1]->getPlayer()]);
         $form = $this->createForm(GameType::class, $game);
         $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
             $game = $form->getData();
             $isScoreValid = $this->gameService->gameInEleven($game);
             $date = new \DateTime('now');
             $game->setPlayedAt($date);
             if (!$isScoreValid){
                 return $this->render('game/newGame.html.twig', [
                     'form' => $form->createView(),
                     'player1' => $player1,
                     'player2' => $player2,
                 ]);
             }
             $this->manager->persist($game);
             $this->manager->flush();
             $this->addflash(
                 'success',
                 "Le score à bien été enregistré"
             );
             if ($goldenRacketId!=null){

                 return $this->redirectToRoute('gridG', [
                     'id' => $goldenRacketId,
                 ]);
             }
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

    /**
     * @Route("/game/{id}/detail", name="detail")
     * @param $id
     * @return Response
     */
    public function detail( $id): Response
    {

        $game = $this->gameRepo->findOneBy(['id' => $id]);
        $jouers = $this->jouerRepo->findBy(['game' => $game->getId()],['id' => 'ASC']);
        $player1 = $this->playerRepo->findOneBy(['id' => $jouers[0]->getPlayer()]);
        $player2 = $this->playerRepo->findOneBy(['id' => $jouers[1]->getPlayer()]);
        $edit = true;
        foreach ($jouers as $jouer){
            if ($jouer->getScore()){
                $edit = false;
            }
        }

                return $this->render('game/detail.html.twig', [
                    'edit' => $edit,
                    'game' => $game,
                    'jouers' => $jouers,
                    'player1' => $player1,
                    'player2' => $player2]);
    }
}
