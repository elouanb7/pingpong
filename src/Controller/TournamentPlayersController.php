<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Tournament;
use App\Entity\TournamentPlayers;
use App\Form\JouerType;
use App\Form\TournamentPlayersType;
use App\Repository\GameRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TournamentPlayersController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager, PlayerRepository $playerRepo)
    {
        $this->manager = $manager;
        $this->playerRepo = $playerRepo;
    }

    /**
     * @Route("/tournament/selectPlayer/{nbJoueurs}", name="tournament_players")
     */
    public function index($nbJoueurs, Request $request): Response
    {
        $form = $this->createForm(TournamentPlayersType::class, null, [
            'nbJoueurs' => $nbJoueurs,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tournament = new Tournament();
            $date = new \DateTime('now');
            $tournament->setCreatedAt($date);
            $tournamentPlayersDatas = $form->getData();
            $countTournamentPlayersDatas = count($tournamentPlayersDatas) + 1;
            for ($i = 1; $i < $countTournamentPlayersDatas ; $i++) {
                for ($j = $i + 1; $j < $countTournamentPlayersDatas; $j++) {
                    if (($tournamentPlayersDatas["player" . $i]->getId()) == ($tournamentPlayersDatas["player" . $j]->getId())) {
                        $this->addflash(
                            'danger',
                            "Un même joueur est sélectionné au moins deux fois !"
                        );
                        return $this->render('tournament_players/select_player.html.twig', [
                            'form' => $form->createView(),
                        ]);
                    }
                }
            }
            foreach ($tournamentPlayersDatas as $tournamentPlayerData) {
                $tournamentPlayer = new TournamentPlayers();
                $tournamentPlayer->setPlayer($tournamentPlayerData);
                $tournamentPlayer->setTournament($tournament)
                    ->setRank(null);
                $this->manager->persist($tournamentPlayer);
            }
            $this->manager->persist($tournament);
            $this->manager->flush();
            $this->addflash(
                'success',
                "Les joueurs ont bien été enregistrés !"
            );
            return $this->redirectToRoute('gridT', [
                'id' => $tournament->getId(),
            ]);
        }
        return $this->render('tournament_players/select_player.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
