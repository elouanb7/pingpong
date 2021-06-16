<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\GoldenRacketPlayersRepository;
use App\Repository\GoldenRacketRepository;
use App\Repository\JouerRepository;
use App\Repository\PlayerRepository;
use App\Service\GoldenRacketService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;

class GoldenRacketController extends AbstractController
{
    private SessionInterface $session;
    private GoldenRacketPlayersRepository $goldenRacketPlayersRepo;
    private GoldenRacketRepository $goldenRacketRepo;
    private GameRepository $gameRepo;
    private PlayerRepository $playerRepo;
    private JouerRepository $jouerRepo;
    private EntityManagerInterface $manager;
    private MailerInterface $mailer;
    private GoldenRacketService $goldenRacketService;

    public function __construct(MailerInterface $mailer, GoldenRacketService $goldenRacketService, SessionInterface $session, GameRepository $gameRepo, JouerRepository $jouerRepo, PlayerRepository $playerRepo, EntityManagerInterface $manager, GoldenRacketPlayersRepository $goldenRacketPlayersRepo, GoldenRacketRepository $goldenRacketRepo)
    {
        $this->goldenRacketRepo = $goldenRacketRepo;
        $this->goldenRacketPlayersRepo = $goldenRacketPlayersRepo;
        $this->gameRepo = $gameRepo;
        $this->jouerRepo = $jouerRepo;
        $this->playerRepo = $playerRepo;
        $this->manager = $manager;
        $this->mailer = $mailer;
        $this->session = $session;
        $this->goldenRacketService = $goldenRacketService;
    }


    /**
     * @Route("/goldenRacket/selectNbG", name="selectNbG")
     */
    public function selectNbG(Request $request): Response
    {
        $nbJoueurs = $request->request->getInt('nbJoueurs');
        if ($nbJoueurs) {
            $this->session->set('nbJoueurs', $nbJoueurs);
            return $this->redirectToRoute('golden_racket_players', [
            ]);
        }
        return $this->render('golden_racket/select_nb_players.html.twig', [
        ]);
    }

    /**
     * @Route("/goldenRacket/{id}/day/{day}", name="golden_day")
     */
    public function day($id, Request $request, $day): Response
    {
        $nextGames = $this->gameRepo->findBy(['goldenRacket' => $id, 'scoreP1' => null, 'scoreP2' => null], ['playedAt' => 'ASC'], 1);
        $this->session->set('goldenRacketId', $id);
        $games = $this->gameRepo->findBy(['goldenRacket' => $id], ['playedAt' => 'ASC']);
        $jouers = $this->jouerRepo->findAll();
        $goldenRacket = $this->goldenRacketRepo->findOneBy(['id' => $id]);
        $players = $this->playerRepo->findAll();
        foreach ($players as $player) {
            $this->goldenRacketService->updateStats($player->getId(), $id);
        }
        return $this->render('golden_racket/day.html.twig', [
            'day' => $day,
            'id' => $id,
            'goldenRacket' => $goldenRacket,
            'nextGames' => $nextGames,
            'games' => $games,
            'jouers' => $jouers,
        ]);
    }

    /**
     * @Route("/goldenRacket/{id}/grid", name="gridG")
     * @throws TransportExceptionInterface
     */
    public function gridOfMatchs($id): Response
    {

        $nextGames = $this->gameRepo->findBy(['goldenRacket' => $id, 'scoreP1' => null, 'scoreP2' => null], ['playedAt' => 'ASC'], 1);
        $this->session->set('goldenRacketId', $id);
        $games = $this->gameRepo->findBy(['goldenRacket' => $id], ['playedAt' => 'ASC']);
        $jouers = $this->jouerRepo->findAll();
        $goldenRacket = $this->goldenRacketRepo->findOneBy(['id' => $id]);
        $players = $this->playerRepo->findAll();
        foreach ($players as $player) {
            $this->goldenRacketService->updateStats($player->getId(), $id);
        }
        $this->goldenRacketService->leaderboard($id);


        $leaderboardP = $this->goldenRacketPlayersRepo->findBy(['goldenRacket' => $id], ['rank' => 'ASC']);
        $playersl = [];
        foreach ($leaderboardP as $playerl) {
            $playerl = $playerl->getPlayer()->getId();
            $playerl = $this->playerRepo->findOneBy(['id' => $playerl]);
            array_push($playersl, $playerl);
        }
        $leaderboard = $playersl;
        $days = $goldenRacket->getDay();
        if ($this->session->get('newDayVar', true)) {
            foreach ($leaderboard as $player) {
                $email = (new TemplatedEmail())
                    ->from(new Address('elouanb7.test@gmail.com', 'PingPong Bot'))
                    ->to($player->getEmail())
                    ->subject('Nouvelle journée - Journée ' . $days . " - Raquette d'or n°" . $goldenRacket->getId())
                    ->htmlTemplate('emails/golden_racket_mail.html.twig')
                    ->context([
                        'date' => new \DateTime('now'),
                        'leaderboard' => $leaderboard,
                        'leaderboardP' => $leaderboardP,
                        'goldenRacket' => $goldenRacket,
                        'nextGames' => $nextGames,
                        'games' => $games,
                        'player' => $player,
                        'jouers' => $jouers,
                        'days' => $days
                    ]);
                $this->mailer->send($email);
            }
            //Message de succès
            $this->addflash(
                'success',
                "La journée à bien démarrée ! Un mail sera envoyé aux différents participants dans peu de temps !"
            );
            $this->session->set('newDayVar', false);
        }
        /* $this->goldenRacketService->doGoldenRacketDay($id);*/
        $response = $this->render('golden_racket/grid_of_matchs.html.twig', [
            'leaderboard' => $leaderboard,
            'leaderboardP' => $leaderboardP,
            'goldenRacket' => $goldenRacket,
            'nextGames' => $nextGames,
            'games' => $games,
            'jouers' => $jouers,
            'days' => $days
        ]);
        $response->setMaxAge(3600);
        return $response;
    }

    /**
     * @Route("/goldenRacket/{id}/newDay", name="newDay")
     */
    public function newDay($id): Response
    {
        $this->goldenRacketService->doGoldenRacketDay($id);
        $this->session->set('newDayVar', true);
        return $this->redirectToRoute('gridG', [
            'id' => $id,
        ]);
    }

    /**
     * @Route("/goldenRacket/{id}/endGoldenRacket", name="endGoldenRacket")
     * @throws TransportExceptionInterface
     */
    public function endGoldenRacket($id): Response
    {
        $nextGames = $this->gameRepo->findBy(['goldenRacket' => $id, 'scoreP1' => null, 'scoreP2' => null], ['playedAt' => 'ASC'], 1);
        $goldenRacket = $this->goldenRacketRepo->findOneBy(['id' => $id]);
        $goldenRacket->setFinishedAt(new \DateTime('now'));
        $games = $this->gameRepo->findBy(['goldenRacket' => $id], ['playedAt' => 'ASC']);
        $jouers = $this->jouerRepo->findAll();
        $days = $goldenRacket->getDay();
        $leaderboardP = $this->goldenRacketPlayersRepo->findBy(['goldenRacket' => $id], ['rank' => 'ASC']);
        $playersl = [];
        foreach ($leaderboardP as $playerl) {
            $playerl = $playerl->getPlayer()->getId();
            $playerl = $this->playerRepo->findOneBy(['id' => $playerl]);
            array_push($playersl, $playerl);
        }
        $leaderboard = $playersl;
        foreach ($leaderboard as $player) {
            $email = (new TemplatedEmail())
                ->from(new Address('elouanb7.test@gmail.com', 'PingPong Bot'))
                ->to($player->getEmail())
                ->subject("Résultats - Raquette d'or n°" . $goldenRacket->getId())
                ->htmlTemplate('emails/end_golden_racket_mail.html.twig')
                ->context([
                    'date' => new \DateTime('now'),
                    'leaderboard' => $leaderboard,
                    'leaderboardP' => $leaderboardP,
                    'goldenRacket' => $goldenRacket,
                    'nextGames' => $nextGames,
                    'games' => $games,
                    'player' => $player,
                    'jouers' => $jouers,
                    'days' => $days
                ]);
            $this->mailer->send($email);
        }
        //Message de succès
        $this->addflash(
            'success',
            "La journée à bien démarrée ! Un mail sera envoyé aux différents participants dans peu de temps !"
        );
        $this->manager->persist($goldenRacket);
        $this->manager->flush();
        return $this->redirectToRoute('home', [
            'id' => $id,
        ]);
    }

    /**
     * @Route("/goldenRacket/liste", name="goldenRackets")
     */
    public function goldenRackets(PaginatorInterface $paginator, Request $request): Response
    {
        $goldenRackets = $this->goldenRacketRepo->findBy([], ['createdAt' => 'DESC']);

        $pagination = $paginator->paginate(
            $goldenRackets,
            $request->query->getInt('page', 1),
            8
        );
        $pagination->setTemplate('ressources/twitter_bootstrap_v4_pagination.html.twig');
        $pagination->setCustomParameters([
            'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination and foundation_v6_pagination)
            'style' => 'bottom',
            'span_class' => 'whatever',
        ]);
        if ($this->getUser()) {
            $playerLogged = $this->playerRepo->findOneBy(['id' => $this->getUser()]);
            $goldenRacketPlayers = $this->goldenRacketPlayersRepo->findBy(['player' => $playerLogged->getId()]);
            return $this->render('golden_racket/golden_rackets.html.twig', [
                '$goldenRackets' => $goldenRackets,
                'pagination' => $pagination,
                'goldenRacketPlayers' => $goldenRacketPlayers,
                'playerLogged' => $playerLogged,
            ]);

        }

        return $this->render('golden_racket/golden_rackets.html.twig', [
            '$goldenRackets' => $goldenRackets,
            'pagination' => $pagination,
        ]);
    }
}

