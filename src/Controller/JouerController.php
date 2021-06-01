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
     * @Route("/game/newGame/player1", name="chooseP1")
     * @param Request $request
     * @return Response
     */
    public function chooseP1(Request $request): Response
    {
        $game = new Game();
        $jouer = new Jouer();
        $form2 = $this->createFormBuilder()
            ->add('player', EntityType::class, [
                'class' => Player::class,
                'choice_label' => function ($player) {
                    return $player->getFullName();
                },
                'disabled' => true,
            ])
            ->getForm();
        $form = $this->createForm(JouerType::class, $jouer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jouer = $form->getData();
            $player1 = $this->playerRepo->findOneBy(['id' => $jouer->getPlayer()->getId()]);
            $date = new \DateTime('now');
            $jouer->setPlayedAt($date);
            $jouer->setGame($game);
            $game->setPlayedAt($date);
            $this->manager->persist($game);
            $this->manager->persist($jouer);
            $this->manager->flush();
            $this->addflash(
                'success',
                "Le joueur 1 à bien été enregistré !"
            );
            return $this->redirectToRoute('chooseP2', [
                'id' => $game->getId(),
                'player1' => $player1,
            ]);
        }
        return $this->render('jouer/Exhibition/player1.html.twig', [
            'form' => $form->createView(),
            'formD' => $form2->createView(),

        ]);
    }

    /**
     * @Route("/game/newGame/{id}/player2", name="chooseP2")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function chooseP2(Request $request, $id): Response
    {
        $jouer = new Jouer();

        $game = $this->gameRepo->findOneBy(['id' => $id]);

        $player1Jouer =  $this->jouerRepo->findOneBy(['game' => $game->getId()]);
        $player1 = $this->playerRepo->findOneBy(['id' => $player1Jouer->getPlayer()]);
        $form = $this->createForm(JouerType::class, $jouer);
        $form->handleRequest($request);
        $form2 = $this->createFormBuilder()
            ->add('player', EntityType::class, [
                'class' => Player::class,
                'choice_label' => function ($player) {
                    return $player->getFullName();
                },
                'disabled' => true,
            ])
            ->getForm();
        if ($form->isSubmitted() && $form->isValid()) {
            $jouer = $form->getData();
            $date = $game->getPlayedAt();
            $jouer->setPlayedAt($date);
            $jouer->setGame($game);
            $this->manager->persist($jouer);
            $this->manager->flush();
            $this->addflash(
                'success',
                "Le joueur 2 à bien été enregistré !"
            );
            return $this->redirectToRoute('newGame', [
                'id' => $game->getId(),
            ]);
        }
        return $this->render('jouer/Exhibition/player2.html.twig', [
            'form' => $form->createView(),
            'formD' => $form2->createView(),
            'player1' => $player1,
        ]);
    }

}
