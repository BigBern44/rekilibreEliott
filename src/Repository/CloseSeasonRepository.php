<?php

namespace App\Repository;

use App\Entity\CloseSeason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method CloseSeason|null find($id, $lockMode = null, $lockVersion = null)
 * @method CloseSeason|null findOneBy(array $criteria, array $orderBy = null)
 * @method CloseSeason[]    findAll()
 * @method CloseSeason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CloseSeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CloseSeason::class);
    }

    public function findThisYear()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.closeDate >= :fromDate AND c.closeDate <= :toDate')
            ->setParameter('fromDate', date("Y").'-01-01')
            ->setParameter('toDate', date("Y").'-12-31')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLastSeason()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.closeDate','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        ;
    }
}
