<?php

namespace App\Repository;

use App\Entity\Registration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Registration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Registration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Registration[]    findAll()
 * @method Registration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Registration::class);
    }

    /**
     * @return String Returns the query for all Activities
     */
    public function findAllPagination($search, $season)
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.dateCreate', 'DESC')
            ->where('r.dateCreate IS NOT NULL AND r.dateCreate >= :season')
            ->andWhere('r.dateValidate IS NULL')
            ->andWhere('r.lastname LIKE :UserName')
            ->setParameter('UserName', '%'.$search.'%')
            ->setParameter('season', $season)
            ->getQuery();
    }

    /**
     * @return String Returns the query for all Activities
     */
    public function findSeason($season, $user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.dateCreate >= :season')
            ->andWhere('r.user = :user')
            ->setParameter('season', $season)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Array Returns the statut of an Adherent
     */
    public function checkStatus($id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT * FROM user
            WHERE id = :Id AND status = 1
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([ 'Id' => $id ]);
        return $stmt->fetchAll();
    }


     /**
     * @return String Returns the query for all Activities
     */
    public function findPaginationNonAdherent($search, $season)
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.dateCreate', 'DESC')
            ->innerJoin('r.user', 'u')
            ->where('r.dateCreate IS NOT NULL AND r.dateCreate >= :season')
            ->andWhere('u.status = 0')
            ->andWhere('r.dateValidate IS NULL')
            ->andWhere('r.lastname LIKE :UserName')
            ->setParameter('UserName', '%'.$search.'%')
            ->setParameter('season', $season)
            ->getQuery();
    }
    
}
