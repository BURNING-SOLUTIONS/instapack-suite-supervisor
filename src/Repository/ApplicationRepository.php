<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    // /**
    //  * @return Application[] Returns an array of Application objects
    //  */

    public function findRolesByApplications($roles): array
    {
        $queryBuilder = $this->createQueryBuilder('a');
        #$rolesJoin = join(',', $roles);
        return $queryBuilder
            ->select('partial a.{id,clientId,name}, partial r.{id,name}')
            ->join('a.roles', 'r')
            ->andWhere($queryBuilder->expr()->in('r.name', $roles))
            //->setParameter('appId', $clientId)
            ->orderBy('a.clientId', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /*
    public function findOneBySomeField($value): ?Application
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
