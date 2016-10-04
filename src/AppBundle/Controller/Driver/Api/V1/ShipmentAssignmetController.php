<?php

namespace AppBundle\Controller\Driver\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ShipmentAssignmetController extends Controller
{
    /**
     * @Route("/assignmentAnswer")
     */
    public function assignmentAnswerAction(Request $req)
    {
        $driverAnswer = $req->getContent();
        $driverAnswer= json_decode($driverAnswer);
        $shipmentId = $driverAnswer->shipmentId;
        $driverId = $driverAnswer->driverId;
        $reason = $driverAnswer->reason;
        $checkAssignTime = $this->get("app.shipment_assignment")
            ->checkAssignTime($shipmentId,$driverId);
        // check time for driver answer is over or not??
        if ($checkAssignTime) {
            // if driver accept request
            if ($driverAnswer->answer) {
                $assignAcceptAction = $this->get('app.shipment_assignment')
                    ->acceptRequest($shipmentId, $driverId);
                if ($assignAcceptAction) {
                    return new JsonResponse(
                        [], 
                        Response::HTTP_NO_CONTENT
                    );
                } else {
                    return new JsonResponse(
                        [], 
                        Response::HTTP_NOT_FOUND
                    );
                }
            } else {  // if driver reject request
                $assignRejectAction = $this->get("app.shipment_assignment")
                    ->rejectRequest($shipmentId, $driverId, $reason);
                if ($assignRejectAction) {
                    return new JsonResponse(
                        [], 
                        Response::HTTP_NO_CONTENT
                    );
                } else {
                    return new JsonResponse(
                        [], 
                        Response::HTTP_NOT_FOUND
                    );
                }
            }
        } else {
            $this->get("app.shipment_assignment")
                ->timeOutAction($shipmentId,$driverId);
            return new JsonResponse(
                [],
                Response::HTTP_REQUEST_TIMEOUT
            );
        }
    }
}