<?php

namespace App\Repository;

use App\Entity\Oauth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Oauth|null find($id, $lockMode = null, $lockVersion = null)
 * @method Oauth|null findOneBy(array $criteria, array $orderBy = null)
 * @method Oauth[]    findAll()
 * @method Oauth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OauthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Oauth::class);
    }

    public function persistOauth(Oauth $oauth): void
    {
        $this->_em->persist($oauth);
        $this->_em->flush();
    }

    public function removeOauth(Oauth $oauth): void
    {
        $this->_em->remove($oauth);
        $this->_em->flush();
    }

    // /**
    //  * @return Oauth[] Returns an array of Oauth objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Oauth
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
