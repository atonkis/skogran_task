<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TransactionController extends AbstractController
{
    /**
     * @Route("/transaction/{page}", name="show_data", methods={"GET"})
     */
    public function show(int $page): Response
    {
        //TODO
        //condition: not exist -> show empty datatables.js
        //after POST action display data; advanced: server-side-pagination
        
    
        return $this->render('transaction/index.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

     /**
     * @Route("/transaction/{page}", name="import_data", methods={"POST"})
     */
    public function import(int $page)
    {
        //TODO 
        //fetch api data
        //check API last date against the stored in database
        //condition: not exist -> use bulk insert way, save to db
    }
}