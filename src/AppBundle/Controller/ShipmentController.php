<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Shipment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use r;

/**
 * Class ShipmentController
 * @package AppBundle\Controller
 */
class ShipmentController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/track_shipment", name="app_track_shipment")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function trackShipmentAction(Request $request)
    {
        $conn = r\connect(
            $this->getParameter('rethinkdb_host'),
            $this->getParameter('rethinkdb_port'),
            'roapp',
            $this->getParameter('rethink_password')
        );
        $result = r\table('shipment')
            ->filter(
                [
                    'tracking_token' => $request->get('token'),
                ]
            )
            ->run($conn);
        /** @var \ArrayObject $current */
        $current = $result->current();
        $trackingToken = $current->getArrayCopy()['tracking_token'];
        $shipmentId = $current->getArrayCopy()['shipment_id'];
        $shipment = $this->getDoctrine()->getRepository("AppBundle:Shipment")->findOneBy(['id' => $shipmentId]);
        if ($shipment instanceof Shipment) {
            $rate = $this->getDoctrine()->getRepository("AppBundle:Rate")->findOneBy(['shipment' => $shipment]);

            return $this->render(
                "shipment/track_shipment.html.twig",
                [
                    'rate' => $rate,
                    'shipment' => $shipment,
                    'tracking_token' => $trackingToken,
                ]
            );
        } else {
            throw new NotFoundHttpException();
        }
    }
}
