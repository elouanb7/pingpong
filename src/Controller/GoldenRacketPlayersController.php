<?php

namespace App\Controller;

use App\Entity\GoldenRacket;
use App\Entity\GoldenRacketPlayers;
use App\Entity\Tournament;
use App\Entity\TournamentPlayers;
use App\Form\GoldenRacketPlayersType;
use App\Form\TournamentPlayersType;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GoldenRacketPlayersController extends AbstractController
{

    private SessionInterface $session;
    private EntityManagerInterface $manager;

    public function __construct(SessionInterface $session, EntityManagerInterface $manager, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo)
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->playerRepo = $playerRepo;
    }

    /**
     * @Route("/goldenRacket/players", name="golden_racket_players")
     */
    public function index(Request $request): Response
    {
        $nbJoueurs = $this->session->get('nbJoueurs');


        $form = $this->createForm(GoldenRacketPlayersType::class, null, [
            'nbJoueurs' => $nbJoueurs,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $goldenRacket = new GoldenRacket();
            $date = new \DateTime('now');
            $goldenRacket->setCreatedAt($date);
            $goldenRacketPlayersDatas = $form->getData();
            foreach ($goldenRacketPlayersDatas as $goldenRacketPlayerData) {
                $goldenRacketPlayer = new GoldenRacketPlayers();
                $goldenRacketPlayer->setPlayer($goldenRacketPlayerData);
                $goldenRacketPlayer->setGoldenRacket($goldenRacket)
                    ->setRank(null);
                $this->manager->persist($goldenRacketPlayer);
            }
            $this->manager->persist($goldenRacket);
            $this->manager->flush();
            $this->addflash(
                'success',
                "Les joueurs ont bien été enregistrés !"
            );

            return $this->redirectToRoute('gridG', [
                'id' => $goldenRacket->getId(),
            ]);
        }
        return $this->render('golden_racket_players/select_player.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
