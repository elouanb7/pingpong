<?php

namespace App\Repository;

use App\Entity\GoldenRacket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GoldenRacket|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoldenRacket|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoldenRacket[]    findAll()
 * @method GoldenRacket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoldenRacketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoldenRacket::class);
    }

    // /**
    //  * @return GoldenRacket[] Returns an array of GoldenRacket objects
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
    public function findOneBySomeField($value): ?GoldenRacket
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
