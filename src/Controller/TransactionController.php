<?php

namespace App\Controller;

use App\Entity\Data;
use App\Repository\DataRepository;
use App\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    /**
     * @Route("/show/{date}", name="show_data", methods={"GET"})
     */
    public function show($date, DataRepository $dr): Response
    {

        return $this->render('transaction/index.html.twig', [
            'data' => $dr->getLimitData(100),
        ]);
    }



    /**
     * @Route("/import/{date}", name="import_data", methods={"POST"})
     */
    public function import($date, TransactionService $ts)
    {

        $dataApi = $ts->getTransactionData($date, $initialPage = 1);

        $ts->ImportData($dataApi);

        return new JsonResponse(['data' => $date]);
    }
}