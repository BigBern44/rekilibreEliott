<?php

namespace App\Repository;

use App\Entity\CategorieDiscussion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorieDiscussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieDiscussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieDiscussion[]    findAll()
 * @method CategorieDiscussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieDiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieDiscussion::class);
    }


    // /**
    //  * @return CategorieDiscussion[] Returns an array of CategorieDiscussion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CategorieDiscussion
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
