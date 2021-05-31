<?php

namespace App\Controller;

use App\Entity\Panne;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Service\ValidationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{


    // Constructeur
    private GameRepository $gameRepo;
    private JouerRepository $jouerRepo;

    public function __construct(GameRepository $gameRepo, JouerRepository $jouerRepo)
    {
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
    }

    /**
     * @Route("/", name="home")
     *
     */
    public function index(): Response
    {
        $games = $this->gameRepo->findAll();
        $jouers = $this->jouerRepo->findAll();
        return $this->render('home/index.html.twig', [
            'games' => $games,
            'jouers' => $jouers,

        ]);
    }
}



