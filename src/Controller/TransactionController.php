<?php

namespace App\Controller;

use App\Repository\DataRepository;
use App\Service\TransactionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    /**
     * @Route("/show", name="show_data", methods={"GET"})
     */
    public function show(DataRepository $dr, TransactionService $ts): Response
    {
        return $this->render('transaction/index.html.twig', [
            'data' => $dr->getLimitData(100),
        ]);
    }



    /**
     * @Route("/import", name="import_data", methods={"POST"})
     */
    public function import(TransactionService $ts, DataRepository $dr) : JsonResponse
    {
        $date = $ts -> getDateForDataApiRequest($this->getParameter('default.initial.date'));

        $dataApi = $ts->getTransactionData($date, $initialPage = 1);

        $ts->ImportData($dataApi);

        return new JsonResponse(['data' => $date]);
    }
}