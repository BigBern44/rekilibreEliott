<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @return String Returns the query for all Payments
     */
    public function findAllPagination($search, $fromDate, $toDate)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->addOrderBy('u.surname', 'ASC')
            ->addOrderBy('u.firstname', 'ASC')
            ->where('u.surname LIKE :UserName')
            ->andWhere('p.date > :fromDate AND p.date < :toDate')
            ->setParameter('UserName', '%' . $search . '%')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate)
            ->getQuery();
    }


    /**
     * @return User[] Returns all payments for export
     */
    public function findAllExport($search, $fromDate, $toDate)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')
            ->where('u.surname LIKE :UserName')
            ->andWhere('p.date > :fromDate AND p.date < :toDate')
            ->setParameter('UserName', '%' . $search . '%')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate)
            ->addOrderBy('p.date', 'ASC')
            ->addOrderBy('u.surname', 'ASC')
            ->addOrderBy('u.firstname', 'ASC')
            ->getQuery()
            ->getResult();
    }






    public function ajouterPaiement($user, $type, $date, $value)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            INSERT INTO payment (user_id, type, value, date)
            values (:user, :type, :value, :date)
            ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user' => $user, 'type' => $type, 'date' => $date, 'value' => $value ]);
        
    }


    




    
}
