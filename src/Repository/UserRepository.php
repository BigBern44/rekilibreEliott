<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[] Returns the query for all Users
     */
    public function findAll()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.firstname', 'ASC')
            ->orderBy('u.surname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns the query for all Users
     */
    public function findAllPagination()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.firstname', 'ASC')
            ->orderBy('u.surname', 'ASC')
            ->getQuery();
    }

    /**
     * @return User[] Returns the query for all Users no interveners
     */
    public function findAllUsersPagination($search)
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.firstname', 'ASC')
            ->orderBy('u.surname', 'ASC')
            ->where('u.surname LIKE :UserName')
            ->setParameter('UserName', '%'.$search.'%')
            ->getQuery();
    }

    /**
     * @return Location[] Returns the query for all Interveners
     */
    public function findAllInterveners()
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.firstname', 'ASC')
            ->orderBy('u.surname', 'ASC')
            ->where('u.intervener = true')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Location[] Returns the query for all Interveners
     */
    public function findAllIntervenersPagination($search)
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.firstname', 'ASC')
            ->orderBy('u.surname', 'ASC')
            ->where('u.intervener = true')
            ->andWhere('u.surname LIKE :IntervenerName')
            ->setParameter('IntervenerName', '%'.$search.'%')
            ->getQuery();
    }

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
   */


    

}
