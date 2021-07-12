<?php

namespace App\Controller;

use App\Entity\Data;
use App\Repository\DataRepository;
use App\Service\TransactionService;
use App\Type\MyDatatableType;
use ArrayIterator;
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
    public function show(Request $request, DataTableFactory $dataTableFactory): Response
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
    public function import(Request $req, TransactionService $ts): JsonResponse
    {

        $date = $ts->getDateForDataApiRequest($this->getParameter('default.initial.date'));

        $dataApi = $ts->getTransactionData($date, $initialPage = 1);

        $importedData =  $ts->ImportData($dataApi);

        return new JsonResponse(['data' => $importedData]);
    }


    /**
     * @Route("/server", name="show_server_processing_data")
     */
    public function showServerProcessingData(Request $request, DataRepository $dataRepository): Response
    {
        $data['recordsTotal'] = $dataRepository->countData();

        return $this->render('transaction/another.html.twig', ['data' => $data]);
    }


    /**
     * @Route("/process", name="server_processing")
     */
    public function serverProcessing(Request $request, DataRepository $dataRepository)
    {
        $draw = intval($request->get('draw'));
        $columns = $request->get('columns');
        $start = $request->get('start');
        $length = $request->get('length');

        // Get results from the Repository
        $results =  $dataRepository -> getRequiredDTData($draw, $start, $length, $columns);

        return $this->json($results);
    }
    
}