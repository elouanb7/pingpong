<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Jouer;
use App\Form\JouerType;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JouerController extends AbstractController
{
    private EntityManagerInterface $manager;
    private GameRepository $gameRepo;

    public function __construct(EntityManagerInterface $manager, GameRepository $gameRepo)
    {
        $this->manager = $manager;
        $this->gameRepo = $gameRepo;
    }

    /**
     * @Route("/game/newGame/player1", name="chooseP1")
     * @param Request $request
     * @return Response
     */
    public function chooseP1(Request $request): Response
    {
        $game = new Game();
        $jouer = new Jouer();

        $form = $this->createForm(JouerType::class, $jouer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jouer = $form->getData();
            $date = new \DateTime('now');
            $jouer->setPlayedAt($date);
            $jouer->setGame($game);
            $this->manager->persist($game);
            $this->manager->persist($jouer);
            $this->manager->flush();
            $this->addflash(
                'success',
                "Le joueur 1 à bien été enregistré !"
            );
            return $this->redirectToRoute('chooseP2', [
                'id' => $game->getId(),
            ]);
        }
        return $this->render('jouer/player1.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/game/newGame/player1", name="chooseP2")
     * @param Request $request
     * @return Response
     */
    public function chooseP2(Request $request): Response
    {
        $jouer = new Jouer();
        $game = $this->gameRepo->findOneBy([]['DESC']);
        $form = $this->createForm(JouerType::class, $jouer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jouer = $form->getData();
            $date = new \DateTime('now');
            $jouer->setPlayedAt($date);
            $jouer->setGame($game);
            $this->manager->persist($jouer);
            $this->manager->flush();
            $this->addflash(
                'success',
                "Le joueur 2 à bien été enregistré !"
            );
            return $this->redirectToRoute('home', [
                'id' => $game->getId()
            ]);
        }
        return $this->render('jouer/player2.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
