<?php

namespace App\Repository;

use App\Entity\Year;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @method Year|null find($id, $lockMode = null, $lockVersion = null)
 * @method Year|null findOneBy(array $criteria, array $orderBy = null)
 * @method Year[]    findAll()
 * @method Year[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YearRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Year::class);
    }

    public function updateYear($year, $date1, $date2, $date3)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            UPDATE year y
            SET first_payment = :date1,
             second_payment = :date2,
             third_payment = :date3
            WHERE year = :year
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['date1' => $date1, 'date2' => $date2, 'date3' => $date3, 'year' => $year  ]);
    }
    public function updateYearMissing($date1, $date2, $date3)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            UPDATE year y
            SET first_payment = :date1,
             second_payment = :date2,
             third_payment = :date3
            WHERE is_current = 1
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['date1' => $date1, 'date2' => $date2, 'date3' => $date3]);
    }


    public function fetchDate()
    {
        return $this->createQueryBuilder('y')
        ->where('y.is_current = 1')
        ->getQuery()
        ->getResult();

        
    }


    public function fectchCurrent()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT id FROM year
            WHERE is_current = 1
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt = $conn->query($sql);
        
    }

}