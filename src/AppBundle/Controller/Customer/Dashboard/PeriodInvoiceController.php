<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\PeriodInvoice;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PeriodInvoiceController
 * @package AppBundle\Controller\Customer\Dashboard
 * @Route("/period_invoice")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class PeriodInvoiceController extends Controller
{
    /**
     * @Route("/", name="app_customer_dashboard_period_invoice_index")
     * @Method("GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository("AppBundle:PeriodInvoice")
            ->createQueryBuilder('p')
            ->join('p.shipments', 's')
            ->join('s.ownerAddress', 'ow')
            ->join('ow.customer', 'c')
            ->where('c.id=:user_id')
            ->setParameter('user_id', $this->getUser()->getId())
            ->getQuery();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('customer/dashboard/period_invoice/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @param Request       $request
     * @param PeriodInvoice $periodInvoice
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param PeriodInvoice $periodInvoice
     * @Route("/{id}", name="app_customer_dashboard_period_invoice_show")
     */
    public function showAction(Request $request, PeriodInvoice $periodInvoice)
    {
        foreach ($periodInvoice->getShipments() as $val) {
            $shipmentIds[] = $val->getId();
        }
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('s')
            ->from('AppBundle:Shipment', 's')
            ->Where($qb->expr()->in('s.id', $shipmentIds))
            ->getQuery()
            ->getResult();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('customer/dashboard/period_invoice/show.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
