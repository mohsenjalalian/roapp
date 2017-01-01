<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\Driver;

/**
 * Class AssignShipmentController
 * @package AppBundle\Controller\Operator\Dashboard
 * @Route("/shipment_assignment")
 * @Security("has_role('ROLE_USER')")
 */
class ShipmentAssignmentController extends Controller
{
    /**
     * @Route("/match/{shipment}/{driver}",name="app_operator_dashboard_shipment_assignment_match")
     * @param Shipment $shipment
     * @param Driver   $driver
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function matchAction(Shipment $shipment = null, Driver $driver = null)
    {
        // if there is shipment and driver with given id
        if ($shipment && $driver) {
            $isSendRequest = $this->get("app.shipment_assignment")
                ->sendRequest($shipment, $driver);
            if ($isSendRequest) {
                $translator = $this->get('translator');
                $this->addFlash('registered_success', $translator->trans('shipment_assigned_successfully'));

                return $this->redirectToRoute(
                    "app_operator_dashboard_shipment_list"
                );
            } else {
                return $this->render(
                    ":operator/dashboard/shipmentAssignment:errorAssignShipment.html.twig",
                    [
                        'shipmentId' => $shipment->getId(),
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
