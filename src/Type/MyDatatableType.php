<?php

namespace App\Type;

use App\Entity\Data;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\NumberColumn;


/**
 * MyDatatableType.
 *
 */
class MyDatatableType implements DataTableTypeInterface
{

     /**
     * {@inheritdoc}
     */
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable
        ->add('transactionId', TextColumn::class, ['label' => 'Transaction Id'])
        ->add('toolNumber', NumberColumn::class, ['label' => 'Tool number'])
        ->add('latitude', NumberColumn::class, ['label' => 'Latitude'])
        ->add('longitude', NumberColumn::class,  ['label' => 'Longitude'])
        ->add('date', DateTimeColumn::class, ['label' => 'Date', 'format' => 'Y-m-d H:i:s'])
        ->add('batPercentage', NumberColumn::class,  ['label' => 'Bat percentage'])
        ->add('importDate', DateTimeColumn::class, ['label' => 'Import date','format' => 'Y-m-d H:i:s'])
        ->createAdapter(ORMAdapter::class, [
            'entity' => Data::class,
        ]);
    }
}