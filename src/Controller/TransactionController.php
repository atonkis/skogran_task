<?php

namespace App\Controller;

use App\Service\TransactionService;
use App\Type\MyDatatableType;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    /**
     * @Route("/show", name="show_data")
     */
    public function show(Request $request, DataTableFactory $dataTableFactory) : Response
    {
        $table = $dataTableFactory->createFromType(MyDatatableType::class)
        ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        
        return $this->render('transaction/index.html.twig', ['datatable' => $table]);
    }


    // /**
    //  * @Route("/import", name="import_data", methods={"POST"})
    //  */
    public function import(TransactionService $ts) : JsonResponse
    {
        $date = $ts -> getDateForDataApiRequest($this->getParameter('default.initial.date'));

        $dataApi = $ts->getTransactionData($date, $initialPage = 1);

        $importedData =  $ts->ImportData($dataApi);

        return new JsonResponse(['data' => $importedData]);
    }
}