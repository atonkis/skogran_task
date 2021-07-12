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
        return $this
            ->createQueryBuilder('d')
            ->orderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->select('d.date')
            ->getQuery()
            ->getOneOrNullResult(Query::HYDRATE_SCALAR);
    }

    public function getLimitData(int $limit)
    {
        return $this
            ->createQueryBuilder('d')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countData()
    {
        return $this
            ->createQueryBuilder('d')
            ->select("count(d.id)")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRequiredDTData($draw, $start, $length, $columns, $orders){

        $dql = $this->createQueryBuilder('d');
        $dqlCountFiltered = $this->createQueryBuilder('d')->select("count(d.id)");
        
        foreach ($columns as $column) {
            if (!empty($column['search']['value'])) {

                $strColSearch = 'd.'.$column['name']." LIKE '%".$column['search']['value']."%'";
                $dql->andWhere($strColSearch);
                $dqlCountFiltered->andWhere($strColSearch);
            }
        }

        foreach ($orders as $order) {
            if (!empty($order['name'])) {
                $dql->addOrderBy('d.'.$order['name'], $order['dir']);
            }
        }
        
        $items = $dql
        ->setFirstResult($start)
        ->setMaxResults($length)
        ->getQuery()
        ->getResult();
        

        $data = [];
        foreach ($items as $entity) {
            $data[] = [
                $entity->getTransactionId(),
                $entity->getToolNumber(),
                $entity->getLatitude(),
                $entity->getLongitude(),
                $entity->getDate() == null ? "": $entity->getDate()->format('Y-m-d H:i:s'),
                $entity->getBatPercentage(),
                $entity->getImportDate() == null ? "": $entity->getImportDate()->format('Y-m-d H:i:s'),
            ];
        }

         $recordsTotal = $this->countData();
        
         $recordsFiltered = $dqlCountFiltered
                             ->getQuery()
                             ->getSingleScalarResult();


        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}