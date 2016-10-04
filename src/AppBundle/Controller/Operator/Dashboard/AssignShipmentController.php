<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\Driver;

class AssignShipmentController extends Controller
{
    /**
     * @Route("/assign_shipment",name="app_operator_dashboard_assign_shipment_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Shipment')
            ->createQueryBuilder('s')
            ->orderBy('s.pickUpTime', 'Asc')
            ->getQuery()
        ;

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render(
            ":operator/dashboard/assignShipment:index.html.twig",
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * @Route("/driver_assign/{shipment}",name="app_operator_dashboard_assign_shipment_driver_assign")
     * @param Shipment $shipment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function driverAssignAction(Shipment $shipment = null, Request $request)
    {
        // check shipment is exist
        if ($shipment) {
            // check shipment rejected by some driver or no
            $banDriver = $this->get("app.shipment_assignment")
                ->filterDriverAction($shipment);

            $query = $this->getDoctrine()
                ->getRepository('AppBundle:Driver')
                ->createQueryBuilder('d')
                ->orderBy('d.fullName', 'Asc')
                ->getQuery()
            ;
            
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10/*limit per page*/
            );

            return $this->render(
                "operator/dashboard/assignShipment/driverListAssign.html.twig",
                [
                    'shipmentId'=>$shipment->getId(),
                    'banDriverList'=>$banDriver,
                    'pagination'=>$pagination
                ]
            );
        } else {
            return $this->redirectToRoute(
                "app_operator_dashboard_assign_shipment_index"
            );
        }
    }

    /**
     * @Route("/assign_set/{shipment}/{driver}",name="app_operator_dashboard_assign_shipment_assign_set")
     * @param Shipment $shipment
     * @param Driver $driver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function assignSetAction(Shipment $shipment = null, Driver $driver = null)
    {
        // if there is shipment and driver with given id
        if ($shipment && $driver) {
            $sendedRequest = $this->get("app.shipment_assignment")
                ->sendRequest($shipment, $driver);
            if ($sendedRequest) {
                return $this->redirectToRoute(
                    "app_operator_dashboard_assign_shipment_index"
                );
            } else {
                return $this->render(
                    ":operator/dashboard/assignShipment:errorAssignShipment.html.twig",
                    [
                        'shipmentId'=>$shipment->getId()
                    ]
                );
            }
        } else {
            return $this->redirectToRoute(
                "app_operator_dashboard_assign_shipment_index"
            );
        }
    }
}