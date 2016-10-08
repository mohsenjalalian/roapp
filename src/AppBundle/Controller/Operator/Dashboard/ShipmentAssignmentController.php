<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\Driver;

/**
 * Class AssignShipmentController
 * @package AppBundle\Controller\Operator\Dashboard
 * @Route("/shipment_assignment")
 */
class ShipmentAssignmentController extends Controller
{
    
    /**
     * @Route("/match/{shipment}/{driver}",name="app_operator_dashboard_shipment_assignment_match")
     * @param Shipment $shipment
     * @param Driver $driver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function matchAction(Shipment $shipment = null, Driver $driver = null)
    {
        // if there is shipment and driver with given id
        if ($shipment && $driver) {
            $sentRequest = $this->get("app.shipment_assignment")
                ->sendRequest($shipment, $driver);
            if ($sentRequest) {
                return $this->redirectToRoute(
                    "app_operator_dashboard_shipment_list"
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
                "app_operator_dashboard_shipment_list"
            );
        }
    }
}