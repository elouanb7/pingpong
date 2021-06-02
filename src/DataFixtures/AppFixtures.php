<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Game;
use App\Repository\GameRepository;
use App\Entity\GoldenRacket;
use App\Entity\GoldenRacketPlayers;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use App\Entity\Tournament;
use App\Entity\TournamentPlayers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    // Propriétés
    private UserPasswordEncoderInterface $encoder;
    private PlayerRepository $playerRepo;
    private GameRepository $gameRepo;

    // Constructeur
    public function __construct(UserPasswordEncoderInterface $encoder, PlayerRepository $playerRepo, GameRepository $gameRepo)
    {
        $this->encoder = $encoder;
        $this->playerRepo = $playerRepo;
        $this->gameRepo = $gameRepo;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        /*$roles = [["ROLE_ADMIN"], ["ROLE_USER"], ["ROLE_CONTRIBUTOR"], ["ROLE_SUPER_ADMIN"]];*/
        // 1 - Je crée mes utilisateurs

        for ($i = 0; $i < 20; $i++){
            $player = new Player();
            $player->setFirstName($faker->firstName())
                ->setRoles(["ROLE_USER"])
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($this->encoder->encodePassword($player, 'password'))
                ->setCreatedAt($faker->dateTimeBetween('-2 years', 'now'));
            $manager->persist($player);
        }
        $manager->flush();

        // 2 - Je récupère mes utilisateurs et je crée un match

        /*$players = $this->playerRepo->findAll();
         for ($i = 0; $i < 10; $i++) {
             $players[$i]->setMatchScore((mt_rand(0, 10)));
             $game = new Game();
             $game->addPlayer($players[$i]);
             $manager->persist($players[$i]);
             $manager->persist($game);
         }
         $manager->flush();
         for ($i = 0; $i < 10; $i++) {
             $game = $this->gameRepo->findOneBy(['id' => $i+1]);
             $j = $i +10;
             $players[$j]->setMatchScore(11);
             $game->addPlayer($players[$j]);
             $manager->persist($players[$j]);
             $game->setScoreP1($players[$i]->getMatchScore());
             $game->setPlayer1($players[$i]);
             $game->setScoreP2($players[$j]->getMatchScore());
             $game->setPlayer2($players[$j]);
             $manager->persist($game);
         }
         $manager->flush();*/


        // 3 - Je boucle (for)

        // 2 Je crée 50 utilisateurs et j'ajoute 10 pannes par utilsateurs

        /*   for ($i = 0; $i < 4; $i++) {
               $categorie = new Categorie();
               $categorie->setName('Categorie n°' . $i)
                   ->setDescription($faker->sentence());
               $manager->persist($categorie);
           }
           $manager->flush();
           // Je crée les users
           for ($j = 0; $j < 10; $j++) {

               // Je crée un nouvel objet User
               $user = new User();
               // Je paramètre mon objet
               $user->setFirstName($faker->firstName())
                   ->setLastName($faker->lastName())
                   ->setEmail($faker->email())
                   ->setRoles($roles[mt_rand(0, 3)])
                   ->setPassword($this->encoder->encodePassword($user, 'password'))
                   ->setCreatedAt($faker->dateTimeBetween('-2 years', 'now'));
               // Je stock en mémoire
               $manager->persist($user);

               for ($k = 0; $k < 10; $k++) {


                   $panne = new Panne();
                   $panne->setCreatedAt(new \DateTime('now'))
                       ->setIntitule($faker->sentence())
                       ->setDescription($faker->paragraph(10))
                       ->setSolution($faker->paragraph(25))
                       ->setUser($user)
                       ->setIsTicket(false);
                   $manager->persist($panne);
               }
               $manager->flush();

               $pannes = $this->panneRepo->findAll();
               // 2 - Boucle sur les pannes (foreach)
               foreach ($pannes as $panne) {
                   $categorie = $this->categorieRepo->findOneBy(['id' => mt_rand(1, 4)]);
                   $panne->setCategorie($categorie);
                   $manager->persist($panne);
               }

           }

           // $product = new Product();
           // $manager->persist($product);

           $manager->flush();*/
    }
}