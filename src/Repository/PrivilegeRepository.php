<?php

namespace App\Repository;

use App\Entity\Privilege;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Privilege|null find($id, $lockMode = null, $lockVersion = null)
 * @method Privilege|null findOneBy(array $criteria, array $orderBy = null)
 * @method Privilege[]    findAll()
 * @method Privilege[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivilegeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Privilege::class);
    }

    // /**
    //  * @return Privilege[] Returns an array of Privilege objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Privilege
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
