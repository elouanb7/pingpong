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

        // 1 - Je crée mes utilisateurs

        for ($i = 0; $i < 40; $i++){
            $player = new Player();
            $player->setFirstName($faker->firstName())
                ->setRoles(["ROLE_USER"])
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($this->encoder->encodePassword($player, 'password'))
                ->setCreatedAt($faker->dateTimeBetween('-1 years', 'now'));
            $manager->persist($player);
        }
        $manager->flush();
    }
}