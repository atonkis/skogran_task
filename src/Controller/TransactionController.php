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
            'data' => $dr->findAll(),
        ]);
    }



    /**
     * @Route("/import/{date}", name="import_data", methods={"POST"})
     */
    public function import($date, TransactionService $ts, EntityManagerInterface $em)
    {

        $dataApi = $ts->getTransactionData($date, $initialPage = 1);

        $canImport = $ts->CanImportData($dataApi);

        if ($canImport) {
            //SQL logger disabled when processing batches to avoid serious impact on performance 
            $em->getConnection()->getConfiguration()->setSQLLogger(null);

            $ts->SaveInBulk($dataApi);
        }

        return new JsonResponse(['data' => $date]);
    }
}