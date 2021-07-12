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


    public function getDateForDataApiRequest(string $date) : string
    {
        $date = $date; //default min date

        $lastDateInDb = $this->dr->getLastDate();

        if(!is_null($lastDateInDb)){
            $date = date('Y-m-d', strtotime($lastDateInDb['date']));
        } 
        
        return $date;
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

    public function ImportData(array $dataApi)
    {

        $mintimeConstrain = strtotime("-1 year", time());
        $maxtimeConstrain = time();
        
        $indexStart = 0;
        $lastDateInDb = $this->dr->getLastDate();
        $lastDateInApi = $dataApi[array_key_last($dataApi)]->date;

        if (is_null($lastDateInDb)) {

            $indexStart = 0; // import all as db empty

        } else if (!is_null($lastDateInDb) and strtotime($lastDateInDb['date']) == strtotime($lastDateInApi)) {
            
            $indexStart = count($dataApi); //nothing to import

        } else if (!is_null($lastDateInDb) and strtotime($lastDateInDb['date']) < strtotime($lastDateInApi)) {

            $indexStart = $this->GetIndexToStart($dataApi, $lastDateInDb['date'], $mintimeConstrain); //start import from returned index
        }

       $importedData = $this->SaveInBulk($dataApi, $indexStart, $mintimeConstrain, $maxtimeConstrain);

       return $importedData;

    }

    public function ImportDataB(array $dataApi)
    {

        $mintimeConstrain = strtotime("-1 year", time());
        $maxtimeConstrain = time();
        
        $indexStart = 0;
        $lastDateInDb = $this->dr->getLastDate();
        $lastDateInApi = $dataApi[array_key_last($dataApi)]->date;

        if (is_null($lastDateInDb)) {

            $indexStart = 0; // import all as db empty

        } else if (!is_null($lastDateInDb) and strtotime($lastDateInDb['date']) == strtotime($lastDateInApi)) {
            
            $indexStart = count($dataApi); //nothing to import

        } else if (!is_null($lastDateInDb) and strtotime($lastDateInDb['date']) < strtotime($lastDateInApi)) {

            $indexStart = $this->GetIndexToStart($dataApi, $lastDateInDb['date'], $mintimeConstrain); //start import from returned index
        }

       $this->SaveInBulkB($dataApi, $indexStart, $mintimeConstrain, $maxtimeConstrain);

    }

    private function GetIndexToStart(array $dataApi, string $lastDateinDb, int $mintimeConstrain)
    {
        
        $lastArrayIndex = count($dataApi) - 1;

        while ($lastArrayIndex > 0) {

            if (strtotime($dataApi[$lastArrayIndex]->date) > $mintimeConstrain && strtotime($dataApi[$lastArrayIndex]->date) <= strtotime($lastDateinDb)) {

                return $lastArrayIndex + 1;
            }

            $lastArrayIndex--;
        }

        return $lastArrayIndex;
    }
    
    private function SaveInBulk(array $dataApi, int $indexStart, int $mintimeConstrain, int $maxtimeConstrain)
    {
        $importedData = array();
        
        $length = count($dataApi);
        $batchSize = 1000;

        for ($i = $indexStart; $i < $length; ++$i) {
            $data = new Data;
            $data->setTransactionId($dataApi[$i]->transaction_id);

            if (strlen($data->getTransactionId()) > 18) continue;

            $data->setToolNumber($dataApi[$i]->tool_number);
            $data->setLatitude($dataApi[$i]->latitude);
            $data->setLongitude($dataApi[$i]->longitude);

            if(strtotime($dataApi[$i]->date)> $maxtimeConstrain || strtotime($dataApi[$i]->date) < $mintimeConstrain) continue;

            $data->setDate(new \DateTime($dataApi[$i]->date));
            $data->setBatPercentage($dataApi[$i]->bat_percentage);
            $data->setImportDate(new \DateTime($dataApi[$i]->import_date));

            array_push($importedData, $dataApi[$i]);
            
            $this->em->persist($data);

            if (($i % $batchSize) === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }
        $this->em->flush();
        $this->em->clear();

        return $importedData;
    }

    private function SaveInBulkB(array $dataApi, int $indexStart, int $mintimeConstrain, int $maxtimeConstrain)
    {
        $length = count($dataApi);
        $batchSize = 1000;

        for ($i = $indexStart; $i < $length; ++$i) {
            $data = new Data;
            $data->setTransactionId($dataApi[$i]->transaction_id);

            if (strlen($data->getTransactionId()) > 18) continue;

            $data->setToolNumber($dataApi[$i]->tool_number);
            $data->setLatitude($dataApi[$i]->latitude);
            $data->setLongitude($dataApi[$i]->longitude);

            if(strtotime($dataApi[$i]->date)> $maxtimeConstrain || strtotime($dataApi[$i]->date) < $mintimeConstrain) continue;

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