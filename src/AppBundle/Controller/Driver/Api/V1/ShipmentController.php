<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\Entity\Shipment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShipmentController
 * @package AppBundle\Controller\Driver\Api\V1
 * @Route("/shipment")
 */
class ShipmentController extends Controller
{
    /**
     * @Route("/fail")
     * @param Request $request
     * @return JsonResponse
     * driver can fail shipment
     */
    public function fail(Request $request)
    {
        $data = json_decode($request->getContent());
        $em = $this->getDoctrine()->getManager();
        $shipment = $this->getDoctrine()
            ->getRepository("AppBundle:Shipment")
            ->find($data->shipmentId);
        if($shipment) {
            $shipment->setStatus(Shipment::STATUS_ASSIGNMENT_FAIL);
            $shipment->setReason($data->reason);
            $em->persist($shipment);

            $em->flush();

            // send sms to customer
            $sendNotification = $this->get("logger");
            $sendNotification->info("your shipment failed By driver");

            return new JsonResponse([],Response::HTTP_NO_CONTENT);
        } else {
            return new JsonResponse(
                [],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}