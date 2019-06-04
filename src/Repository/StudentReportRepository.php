<?php

namespace App\Repository;

use App\Entity\StudentReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StudentReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentReport[]    findAll()
 * @method StudentReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentReportRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StudentReport::class);
    }

    // /**
    //  * @return StudentReport[] Returns an array of StudentReport objects
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
    public function findOneBySomeField($value): ?StudentReport
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
