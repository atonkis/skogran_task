<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TransactionService
{

    private $httpClient;
    private $transactionApiKey;
    private $mergedDataArray;

    public function  __construct(HttpClientInterface $httpClient, $transactionApiKey)
    {
        $this->httpClient = $httpClient;
        $this->transactionApiKey = $transactionApiKey;
        $this->mergedDataArray = array();
    }

    public function getTransactionData($date, $initialPage): array
    {
        $decodedApiData = $this->getDataFromApi($date, $initialPage);

        $pages  = $decodedApiData->pages;
        $currentPage = $decodedApiData->current_page;
        $this->mergedDataArray = $decodedApiData->data;

        while ($currentPage < 40) {
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
}