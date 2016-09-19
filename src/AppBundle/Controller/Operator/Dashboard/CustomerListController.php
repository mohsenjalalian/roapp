<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CustomerListController extends Controller
{
    /**
     * @Route("/customerList",name="operator_dashboard_customerList")
     */
    public function getAllCustomerAction(){
        return $this->render
        (
            'operator/dashboard/customerList/customerList.html.twig'
        );
    }
}