<?php

namespace AppBundle\Controller\Customer\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SubscriptionInvoiceController
 * @package AppBundle\Controller\Customer\Dashboard
 * @Route("/subscription_invoice")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class SubscriptionInvoiceController extends Controller
{
    /**
     * @Route("/", name="app_customer_dashboard_subscription_invoice_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $subscriptionInvoicesQuery = $em->getRepository("AppBundle:SubscriptionInvoice")
            ->createQueryBuilder('s')
            ->where('s.businessUnit=:user_business_unit')
            ->setParameter('user_business_unit', $this->getUser()->getBusinessUnit())
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $subscriptionInvoicesQuery, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('customer/dashboard/subscription_invoice/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
