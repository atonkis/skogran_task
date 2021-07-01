<?php

namespace App\Service;

use App\Entity\Data;
use App\Repository\DataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TransactionService
{

    private $httpClient;
    private $transactionApiKey;
    private $mergedDataArray;
    private EntityManagerInterface $em;
    private DataRepository $dr;

    public function  __construct(HttpClientInterface $httpClient, $transactionApiKey, EntityManagerInterface $em, DataRepository $dr)
    {
        $this->httpClient = $httpClient;
        $this->transactionApiKey = $transactionApiKey;
        $this->mergedDataArray = array();
        $this->em = $em;
        $this->dr = $dr;
    }

    public function getTransactionData($date, $initialPage)
    {
        $decodedApiData = $this->getDataFromApi($date, $initialPage);

        $pages  = $decodedApiData->pages;
        $currentPage = $decodedApiData->current_page;
        $this->mergedDataArray = $decodedApiData->data;

        while ($currentPage < $pages) {
            return $this->mergedDataArray = array_merge($this->mergedDataArray, $this->getTransactionData($date, ++$currentPage));
        }

        return  $this->mergedDataArray;
    }
    

    private function getDataFromApi($date, $page)
    {
        $url = "http://159.65.123.24/data/export/{$date}/{$page}";

        $response = $this->httpClient->request('GET', $url, [
            'query' => [
                'api-key' => $this->transactionApiKey
            ]
        ]);

        if ($response->getStatusCode() !== 200) {
            return new JsonResponse('API transactions error', 400);
        }

        $apiData = $response->getContent();

        return json_decode($apiData);
    }


    public function CanImportData(Array $dataApi){
        
        if(!isset($dataApi)) return true;

        $lastDateInApi = $dataApi[array_key_last($dataApi)]->date;

        $lastDateInDb = $this->dr->getLastDate();

        if(!is_null($lastDateInDb) and strcmp($lastDateInDb['date'], $lastDateInApi)==0) return false;

        return true;
        
    }

    public function SaveInBulk(Array $dataApi){

        $batchSize = 1000;
        
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

             $this->em->persist($data);
            
            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear(); 
            }
        }
        $this->em->flush(); 
        $this->em->clear();

    }
}