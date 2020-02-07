<?php

namespace App\Repository;

use App\Entity\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permission[]    findAll()
 * @method Permission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    // /**
    //  * @return Permission[] Returns an array of Permission objects
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
    public function findOneBySomeField($value): ?Permission
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    public function getAllPermissionsByRoles($roles): array
    {
        $result = array();
        $queryBuilder = $this->createQueryBuilder('p');
        #$rolesJoin = join(',', $roles);
        $permissions = $queryBuilder
            ->select('partial p.{id, description}, partial r.{id,name}, partial a.{id,name,clientId}, partial pr.{id,name}')
            ->join('p.role', 'r')
            ->join('p.application', 'a')
            ->join('p.privilege', 'pr')
            ->andWhere($queryBuilder->expr()->in('r.name', $roles))
            /*->groupBy('a.name', 'ASC')*/
            ->getQuery()
            ->getArrayResult();

        foreach ($permissions as $permission) {
            ['name' => $appName, 'clientId' => $appId] = $permission['application'];
            ['name' => $rolName, 'id' => $rolId] = $permission['role'];
            ['name' => $privilegeName, 'id' => $privilegeId] = $permission['privilege'];
            $appExistInAsociativeResult = array_key_exists($appName, $result);

            if (!$appExistInAsociativeResult) {
                $result[$appName] = array(
                    'appId' => $appId,
                    'roles' => array(
                        $rolName => array(
                            'privileges' => array($privilegeName)
                        )
                    )
                );
            } else {
                $roleExistInApp = array_key_exists($rolName, $result[$appName]['roles']);

                if ($roleExistInApp) {
                    array_push($result[$appName]['roles'][$rolName]['privileges'], $privilegeName);
                } else {
                    array_push($result[$appName]['roles'][$rolName], array(
                        'privileges' => array($privilegeName)
                    ));
                }
            };
        }

        return $result;

    }
}
