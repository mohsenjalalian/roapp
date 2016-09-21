<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\Customer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CustomerListController extends Controller
{
    /**
     * @Route("/customerList",name="operator_dashboard_customerList")
     */
    public function getAllCustomerAction(Request $request){
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM AppBundle:Customer a ORDER BY a.username";
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );
        return $this->render
        (
            'operator/dashboard/customerList/customerList.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }
}