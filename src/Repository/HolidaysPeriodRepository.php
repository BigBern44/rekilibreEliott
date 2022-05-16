<?php

namespace App\Repository;

use App\Entity\HolidaysPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method HolidaysPeriod|null find($id, $lockMode = null, $lockVersion = null)
 * @method HolidaysPeriod|null findOneBy(array $criteria, array $orderBy = null)
 * @method HolidaysPeriod[]    findAll()
 * @method HolidaysPeriod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HolidaysPeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HolidaysPeriod::class);
    }

    // /**
    //  * @return HolidaysPeriod[] Returns an array of HolidaysPeriod objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HolidaysPeriod
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
