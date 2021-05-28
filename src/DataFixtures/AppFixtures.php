<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Game;
use App\Entity\GoldenRacket;
use App\Entity\GoldenRacketPlayers;
use App\Entity\Player;
use App\Entity\Tournament;
use App\Entity\TournamentPlayers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    // Propriétés
    private UserPasswordEncoderInterface $encoder;

    // Constructeur
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $roles = [["ROLE_ADMIN"], ["ROLE_USER"], ["ROLE_CONTRIBUTOR"], ["ROLE_SUPER_ADMIN"]];
        // 1 - Récupérer toutes les pannes


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