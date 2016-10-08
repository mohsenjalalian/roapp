<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\Entity\AssignmentRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AssignmentShipmentController
 * @package AppBundle\Controller\Driver\Api\V1
 * @Route("/shipment_assignment")
 */
class ShipmentAssignmentController extends Controller
{

    /**
     * @Route("/{assignment}/accept")
     * @param $assignment
     * @return JsonResponse
     */
    public function acceptAction(AssignmentRequest $assignment)
    {
        $isAssignTimeExpire = $this->get("app.shipment_assignment")
            ->isAssignTimeExpire($assignment);
        if(!$isAssignTimeExpire) {
            $this->get('app.shipment_assignment')
                ->acceptRequest($assignment);

                return new JsonResponse
                (
                    [],
                    Response::HTTP_NO_CONTENT
                );
        } else {
            $this->get("app.shipment_assignment")
                ->timeOutAction($assignment);
            return new JsonResponse
            (
                [],
                Response::HTTP_REQUEST_TIMEOUT
            );
        }
    }

    /**
     * @Route("/{assignment}/reject")
     * @param AssignmentRequest $assignment
     * @return JsonResponse
     */
    public function rejectAction(AssignmentRequest $assignment , Request $req)
    {
        $isAssignTimeExpire = $this->get("app.shipment_assignment")
            ->isAssignTimeExpire($assignment);
        if(!$isAssignTimeExpire) {
            $reason = $req->getContent();
            $reason = json_decode($reason);
            $this->get("app.shipment_assignment")
                ->rejectRequest($assignment,$reason->reason);

            return new JsonResponse(
                [],
                Response::HTTP_NO_CONTENT
            );
        } else {
            $this->get("app.shipment_assignment")
                ->timeOutAction($assignment);
            return new JsonResponse(
                [],
                Response::HTTP_REQUEST_TIMEOUT
            );
        }
    }
}