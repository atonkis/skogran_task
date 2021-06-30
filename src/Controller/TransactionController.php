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
use Symfony\Component\Serializer\SerializerInterface;

class TransactionController extends AbstractController
{
    /**
     * @Route("/show/{date}", name="show_data", methods={"GET"})
     */
    public function show($date = "2021-03-01", EntityManagerInterface $em, SerializerInterface $serializer) : Response
    {

         $repository = $em->getRepository(Data::class);
         $allData = $repository->findAll();
        //  $response = $serializer->serialize($allData, 'json', ['groups' => 'default']);

        return $this->render('transaction/index.html.twig', [
            'data' => $allData,
        ]);
    }


    
    /**
     * @Route("/import/{date}", name="import_data", methods={"POST"})
     */
    public function import($date = "2021-03-01", Request $request, TransactionService $ts, EntityManagerInterface $em, DataRepository $dr)
    {


        $dataApi = $ts->getTransactionData("2021-03-01", $initialPage = 1);

        if(is_null($dataApi) || empty($dataApi)) return new JsonResponse(['data' => []]);

        
        $lastDateInApi = $dataApi[array_key_last($dataApi)]->date;

        $lastDateInDb = $dr->getLastDate();

        if(!is_null($lastDateInDb) and strcmp($lastDateInApi, $lastDateInApi)==0) return new JsonResponse(['data' => []]);
        
        //SQL logger disabled when processing batches to avoid serious impact on performance 
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $batchSize = 500;
        
        $length = count($dataApi);

        
        for ($i = 0; $i < $length; ++$i) {
            $data = new Data;
            $data->setTransactionid($dataApi[$i]->transaction_id);

            if(strlen($data->getTransactionid())>18) continue;
            
            $data->setToolNumber($dataApi[$i]->tool_number);
            $data->setLatitude($dataApi[$i]->latitude);
            $data->setLongitude($dataApi[$i]->longitude);
            $data->setDate(new \DateTime($dataApi[$i]->date));

            $data->setBatPercentage($dataApi[$i]->bat_percentage);
            $data->setImportDate(new \DateTime($dataApi[$i]->import_date));

            $em->persist($data);
            
            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
            }
        }
        $em->flush(); // Persist objects that did not make up an entire batch
        $em->clear();


        return new JsonResponse(['data' => $date]);
    }
}   