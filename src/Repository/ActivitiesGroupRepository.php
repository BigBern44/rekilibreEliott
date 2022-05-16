<?php


namespace App\Repository;

use App\Entity\ActivitiesGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method ActivitiesGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivitiesGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivitiesGroup[]    findAll()
 * @method ActivitiesGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivitiesGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivitiesGroup::class);
    }

    /**
     * @return String Returns the query for all Activities
     */
    public function findAllPagination()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.name', 'ASC')
            ->getQuery();
    }

    

}