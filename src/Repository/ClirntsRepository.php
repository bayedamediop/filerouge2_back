<?php

namespace App\Repository;

use App\Entity\Clirnts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Clirnts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clirnts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clirnts[]    findAll()
 * @method Clirnts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClirntsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clirnts::class);
    }

    // /**
    //  * @return Clirnts[] Returns an array of Clirnts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Clirnts
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
