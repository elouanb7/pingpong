<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\ChoosePlayerType;
use App\Form\PlayerType;
use App\Repository\GameRepository;
use App\Repository\GoldenRacketPlayersRepository;
use App\Repository\GoldenRacketRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Repository\TournamentPlayersRepository;
use App\Repository\TournamentRepository;
use App\Service\StatsService;
use App\Service\ValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PlayerController extends AbstractController
{

    private GoldenRacketPlayersRepository $goldenRacketPlayersRepo;
    private GoldenRacketRepository $goldenRacketRepo;
    private GameRepository $gameRepo;
    private JouerRepository $jouerRepo;
    private StatsService $statsService;
    private ValidationService $validationService;
    private PlayerRepository $playerRepo;
    private TournamentRepository $tournamentRepo;
    private TournamentPlayersRepository $tournamentPlayersRepo;
    private SessionInterface $session;


    public function __construct(ValidationService $validationService, TournamentPlayersRepository $tournamentPlayersRepo, SessionInterface $session, GoldenRacketPlayersRepository $goldenRacketPlayersRepo, GoldenRacketRepository $goldenRacketRepo, GameRepository $gameRepo, JouerRepository $jouerRepo, StatsService $statsService, PlayerRepository $playerRepo, TournamentRepository $tournamentRepo)
    {
        $this->gameRepo = $gameRepo;
        $this->tournamentRepo = $tournamentRepo;
        $this->tournamentPlayersRepo = $tournamentPlayersRepo;
        $this->jouerRepo = $jouerRepo;
        $this->statsService = $statsService;
        $this->validationService = $validationService;
        $this->playerRepo = $playerRepo;
        $this->goldenRacketRepo = $goldenRacketRepo;
        $this->goldenRacketPlayersRepo = $goldenRacketPlayersRepo;
        $this->session = $session;
    }

    /**
     * @Route("/profile/{id}", name="profile")
     */
    public function profile($id): Response
    {
        $player = $this->playerRepo->findOneBy(['id' => $id]);
        $games = [];
        $jouersP = $this->jouerRepo->findBy(['player' => $id],['playedAt' => 'DESC'], );
        $jouers = $this->jouerRepo->findAll();
        $tournaments = [];
        $tournamentPlayers = $this->tournamentPlayersRepo->findBy(['player' => $id]);
        $goldenRackets = [];
        $goldenRacketPlayers = $this->goldenRacketPlayersRepo->findBy(['player' => $id]);
        $validJouers = [];
        foreach ($jouersP as $jouerP){
            if (!$jouerP->getScore()==null){
                array_push($validJouers, $jouerP);
            }
        }
        $jouersP = $validJouers;
        foreach ($jouersP as $jouerP){
            $game = $jouerP->getGame()->getId();
            $game = $this->gameRepo->findOneBy(['id' => $game]);
            if (($game->getIsTournament()==true)){}
            elseif ($game->getIsGoldenRacket()==true){}
            else{
                array_push($games,$game);
            }
        }
        foreach ($tournamentPlayers as $tournamentPlayer){
            $tournament = $this->tournamentRepo->findOneBy(['id' => $tournamentPlayer->getTournament()]);
            array_push($tournaments, $tournament);
        }
        foreach ($goldenRacketPlayers as $goldenRacketPlayer){
            $goldenRacket = $this->goldenRacketRepo->findOneBy(['id' => $goldenRacketPlayer->getGoldenRacket()]);
            array_push($goldenRackets, $goldenRacket);
        }
        $players = $this->playerRepo->findAll();
        foreach ($players as $playerS){
            $this->statsService->matchsStats($playerS);
            $this->statsService->tournamentStats($playerS);
            $this->statsService->goldenRacketStats($playerS);
        }

        $response = $this->render('player/profile.html.twig', [
            'player' => $player,
            'games' => $games,
            'jouers' => $jouers,
            'jouersP' => $jouersP,
            'tournaments' => $tournaments,
            'tournamentPlayers' => $tournamentPlayers,
            'goldenRackets' => $goldenRackets,
            'goldenRacketPlayers' => $goldenRacketPlayers,
        ]);
        $response->setMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/leaderboard", name="leaderboard")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function leaderboard(Request $request, EntityManagerInterface $manager): Response
    {
        $players = $this->playerRepo->FindAll();
        return $this->render('player/leaderboard.html.twig', [
            'players' => $players
        ]);
    }

    /**
     * @Route("/admin/dashboard", name="dashboard")
     */
    public function dashboard(): Response
    {
        $response = $this->render('admin/dashboard.html.twig',[]);
        $response->setMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/admin/add_player", name="add_player")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function addPlayer(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $player = new Player();
        $form = $this->createForm(PlayerType::class, $player);

        $form->handleRequest($request);
        //Je vérifie le formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //Je récupère mes données du formulaire
            $player = $form->getData();
            //Abcdef3!
            $plainPassword = $form->get('plainPassword')->getData();
            $violations = $this->validationService->setPasswordViolation($plainPassword);
            if (0 !== count($violations)) {
                return $this->render('admin/player_add.html.twig', [
                    'form' => $form->createView(),
                    'violations' => $violations,
                ]);
            }
            $player->setPassword($passwordEncoder->encodePassword($player, $plainPassword));
            $player->setRoles($form->get('roles')->getData());
            //Je met à jour la date
            $player->setCreatedAt(new \DateTime('now'));
            //Je persiste mes données
            $manager->persist($player);
            //J'enregistre mes données
            $manager->flush();

            //Message de succès

            $this->addflash(
                'success',
                "Le nouveau Joueur à bien été enregistré !"
            );

            return $this->redirectToRoute('profile', [
                'id' => $player->getId()
            ]);
        }

        return $this->render('admin/player_add.html.twig', [
            'form' => $form->createView(),
           ]);
    }

    /**
     * @Route("/admin/edit_player/choose", name="choose_edit")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function chooseEdit(Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ChoosePlayerType::class);
        $form->handleRequest($request);
        //Je vérifie le formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //Je récupère mes données du formulaire
            $player = $form->getData();


            $this->addflash(
                'success',
                "Le joueur à bien été sélectionné !"
            );

            return $this->redirectToRoute('edit_player', [
                'id' => $player["player"]->getId()
            ]);
        }

        return $this->render('admin/choose.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/edit_player/{id}", name="edit_player")
     * @param Player $player
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function editPlayer(Player $player, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $player = $this->playerRepo->findOneBy(['id' => $player]);
        $form = $this->createForm(PlayerType::class, $player, []);

        $form->handleRequest($request);
        //Je vérifie le formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //Je récupère mes données du formulaire
            $player = $form->getData();
            //Abcdef3!
            $plainPassword = $form->get('plainPassword')->getData();
            $violations = $this->validationService->setPasswordViolation($plainPassword);
            if (0 !== count($violations)) {
                return $this->render('admin/player_edit.html.twig', [
                    'form' => $form->createView(),
                    'violations' => $violations,
                ]);
            }
            $player->setPassword($passwordEncoder->encodePassword($player, $plainPassword));
            $player->setRoles($form->get('roles')->getData());
            //Je met à jour la date
            $player->setCreatedAt(new \DateTime('now'));
            //Je persiste mes données
            $manager->persist($player);
            //J'enregistre mes données
            $manager->flush();

            //Message de succès
            $this->addflash(
                'success',
                "Les modifications ont bien été enregistrées !"
            );

            return $this->redirectToRoute('profile', [
                'id' => $player->getId()
            ]);
        }

        return $this->render('admin/player_edit.html.twig', [
            'form' => $form->createView(),
            'player' => $player,
        ]);
    }

    /**
     * @Route("/admin/del_player/choose", name="choose_del")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function chooseDel(Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ChoosePlayerType::class);
        $form->handleRequest($request);
        //Je vérifie le formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //Je récupère mes données du formulaire
            $player = $form->getData();
            $player = $player["player"]->getId();
            $player = $this->playerRepo->findOneBy(['id' => $player]);
            //J'affiche la vue

            return $this->redirectToRoute('del_confirm', [
                'id' => $player->getId()
            ]);
        }

        return $this->render('admin/choose.html.twig', [
            'form' => $form->createView(),
            'delete' => true,
        ]);
    }

    /**
     * @Route("/admin/del_player/confirm/{id}", name="del_confirm")
     * @param Player $player
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function confirmDel(Player $player,Request $request, EntityManagerInterface $manager): Response
    {
        return $this->render('admin/confirm_del.twig', [
            'player' => $player,
        ]);
    }
    /**
     * @Route("/admin/del_player/{id}", name="del_player")
     * @param Player $player
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delPlayer(Player $player,Request $request, EntityManagerInterface $manager): Response
    {
            $manager->remove($player);
            //
            $manager->flush();
            //
            $this->addFlash(
                'success',
                 "" . $player->getFullName() . " à bien été supprimé !"
            );
            //J'affiche la vue
            return $this->redirectToRoute('dashboard', []);
        }

}
