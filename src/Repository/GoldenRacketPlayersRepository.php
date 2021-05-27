<?php

namespace App\Repository;

use App\Entity\GoldenRacketPlayers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GoldenRacketPlayers|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoldenRacketPlayers|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoldenRacketPlayers[]    findAll()
 * @method GoldenRacketPlayers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoldenRacketPlayersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoldenRacketPlayers::class);
    }

    // /**
    //  * @return GoldenRacketPlayers[] Returns an array of GoldenRacketPlayers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GoldenRacketPlayers
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
