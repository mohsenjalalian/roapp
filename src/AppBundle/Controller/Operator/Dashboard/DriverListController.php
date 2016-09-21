<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DriverListController extends Controller
{
    /**
     * @Route("/driverList",name="operator_dashboard_driverList")
     */
    public function getAllDriverAction(Request $request){
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT d FROM AppBundle:Driver d ORDER BY d.username";
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render
        (
            'operator/dashboard/driverList/driverList.html.twig',
            [
                'pagination' => $pagination
            ]
        );

    }
}