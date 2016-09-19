<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DriverListController extends Controller
{
    /**
     * @Route("/driverList",name="operator_dashboard_driverList")
     */
    public function getAllDriverAction(){
        return $this->render
        (
            'operator/dashboard/driverList/driverList.html.twig'
        );
    }
}