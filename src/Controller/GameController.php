<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Jouer;
use App\Form\GameType;
use App\Form\JouerType;
use Container3zb72ft\getGameTypeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class GameController extends AbstractController
{

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
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


}
