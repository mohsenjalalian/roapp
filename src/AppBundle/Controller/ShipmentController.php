<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
        $conn = r\connect('localhost', '28015', 'roapp', $this->getParameter('rethink_password'));
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

        return $this->render(
            "track_shipment.html.twig",
            [
               'tracking_token' => $trackingToken,
            ]
        );
    }
}
