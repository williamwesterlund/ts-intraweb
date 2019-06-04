<?php

namespace App\Repository;

use App\Entity\StudentReports;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StudentReports|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentReports|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentReports[]    findAll()
 * @method StudentReports[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentReportsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StudentReports::class);
    }

    // /**
    //  * @return StudentReports[] Returns an array of StudentReports objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StudentReports
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
