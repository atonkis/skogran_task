<?php

namespace App\Repository;

use App\Entity\Data;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Data|null find($id, $lockMode = null, $lockVersion = null)
 * @method Data|null findOneBy(array $criteria, array $orderBy = null)
 * @method Data[]    findAll()
 * @method Data[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Data::class);
    }

    public function getLastDate()
    {
        return $this->createQueryBuilder('d')
        ->orderBy('d.id', 'DESC')
        ->setMaxResults(1)
        ->select('d.date')
        ->getQuery()
        ->getOneOrNullResult(Query::HYDRATE_SCALAR);
    }

    public function getLimitData(int $limit)
    {
        return $this->createQueryBuilder('d')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
    }
   
}