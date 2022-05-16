<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\Mapping as ORM;




/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function findAll()
    {
        return $this->findBy(['a.name' => 'ASC', 'a.day' => 'DESC']);
    }

    /**
     * @return String Returns the query for all Activities
     */
    public function findAllPagination()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.day', 'ASC')
            ->orderBy('a.name', 'ASC')
            ->getQuery();
    }

    /**
     * @return String Returns Doctrine\ORM\QueryBuilder for all Hebdo Activities in form
     */
    public function findAllHebdo($season)
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.day', 'ASC')
            ->orderBy('a.name', 'ASC')
            ->where('a.type = :ActivityType')
            ->andWhere('a.fromDateTime > :fromSeason AND a.toDateTime < :toSeason')
            ->setParameter('ActivityType', 'hebdo')
            ->setParameter('fromSeason', $season)
            ->setParameter('toSeason', ($season->format('Y')+1).'-'.$season->format('m-d'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return String Returns Doctrine\ORM\QueryBuilder for all Ponctual Activities in form
     */
    public function findAllPonctual()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.name', 'ASC')
            ->orderBy('a.fromDateTime', 'ASC')
            ->where('a.type = :ActivityType')
            ->andWhere('a.toDateTime > :thisDate')
            ->setParameter('ActivityType', 'ponctual')
            ->setParameter('thisDate', new \DateTime('-1 day'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return String Returns Doctrine\ORM\QueryBuilder for all Ponctual Activities in form
     */
    public function findAllPonctualCalendar()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.fromDateTime', 'ASC')
            ->where('a.type = :ActivityType')
            ->andWhere('a.toDateTime > :thisDate')
            ->setParameter('ActivityType', 'ponctual')
            ->setParameter('thisDate', new \DateTime('-1 day'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return String Returns the query for all Hebdo Activities
     */
    public function findAllHebdoPagination($search, $season)
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.day', 'ASC')
            ->orderBy('a.name', 'ASC')
            ->where('a.type = :ActivityType')
            ->andWhere('a.name LIKE :ActivityName')
            ->andWhere('a.fromDateTime > :fromSeason AND a.toDateTime < :toSeason')
            ->setParameters([
                'ActivityName' => '%'.$search.'%',
                'ActivityType' => 'hebdo',
                'fromSeason' => $season.'-08-23',
                'toSeason' => ($season+1).'-08-23',
            ])
            ->getQuery();
    }

    /**
     * @return String Returns the query for all Ponctual Activities
     */
    public function findAllPonctualPagination($search,$season)
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.name', 'ASC')
            ->orderBy('a.fromDateTime', 'ASC')
            ->where('a.type = :ActivityType')
            ->andWhere('a.name LIKE :ActivityName')
            ->andWhere('a.fromDateTime > :fromSeason AND a.toDateTime < :toSeason')
            ->setParameters([
                'ActivityName' => '%'.$search.'%',
                'ActivityType' => 'ponctual',
                'fromSeason' => $season.'-08-23',
                'toSeason' => ($season+1).'-08-23',
            ])
            ->getQuery();
    }



    public function findPaginationByDate($fromDate, $toDate, $id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT name,from_date_time, to_date_time, from_time, to_time, day, u.surname, u.firstname FROM activity a
            INNER JOIN interveneractivity_intervener iai ON iai.intervener_id = a.id  
            INNER JOIN activity_user au ON au.user_id = a.id 
            INNER JOIN user u ON u.id = au.activity_id
            WHERE ((a.from_date_time >= :fromDate AND a.from_date_time <= :toDate) OR a.to_date_time >= :fromDate AND a.to_date_time <= :toDate) AND :id = iai.interveneractivity_id 
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['fromDate' => $fromDate, 'toDate' => $toDate, 'id' => $id ]);
        return $stmt->fetchAll();
    }

}
