<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CustomerListController extends Controller
{
    /**
     * @Route("/customerList",name="operator_dashboard_customerList")
     */
    public function getAllCustomerAction(){
        $customerTbl = new Customer();
        $customerList = $this->getDoctrine()
            ->getRepository("AppBundle:Customer")
            ->findAll();
        return $this->render
        (
            'operator/dashboard/customerList/customerList.html.twig',
            [
                'customerList'=>$customerList
            ]
        );
    }
}