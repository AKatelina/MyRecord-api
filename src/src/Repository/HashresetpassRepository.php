<?php

namespace App\Repository;

use App\Entity\Hashresetpass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hashresetpass|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hashresetpass|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hashresetpass[]    findAll()
 * @method Hashresetpass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HashresetpassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hashresetpass::class);
    }



    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
