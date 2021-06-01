<?php

namespace App\Controller;

use App\Entity\Panne;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{


    // Constructeur
    private GameRepository $gameRepo;
    private JouerRepository $jouerRepo;
    private StatsService $statsService;
    private PlayerRepository $playerRepo;

    public function __construct(GameRepository $gameRepo, JouerRepository $jouerRepo, StatsService $statsService, PlayerRepository $playerRepo)
    {
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->statsService = $statsService;
        $this->playerRepo = $playerRepo;
    }

    /**
     * @Route("/", name="home")
     *
     */
    public function index(): Response
    {
        $games = $this->gameRepo->findBy([],['playedAt' => 'DESC'], 6);
        $allGames = $this->gameRepo->findBy([],['playedAt' => 'DESC']);
        $jouers = $this->jouerRepo->findAll();

        $player = $this->playerRepo->findOneBy(['id' => $this->getUser()]);
        if ($this->getUser()){
            $this->statsService->matchStats($player);
        }

        return $this->render('home/index.html.twig', [
            'games' => $games,
            'jouers' => $jouers,
            'allGames' => $allGames,


        ]);
    }
}



