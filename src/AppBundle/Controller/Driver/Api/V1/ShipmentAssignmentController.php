<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\Entity\ShipmentAssignment;
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
     * @param ShipmentAssignment $assignment
     * @return JsonResponse
     */
    public function acceptAction(ShipmentAssignment $assignment)
    {
        $isAssignTimeExpire = $this->get("app.shipment_assignment")
            ->isExpiredAssignTime($assignment);
        if($isAssignTimeExpire) {
            $this->get('app.shipment_assignment')
                ->acceptRequest($assignment);

                return new JsonResponse(
                    [],
                    Response::HTTP_NO_CONTENT
                );
        } else {
            $this->get("app.shipment_assignment")
                ->timeOutAction($assignment);
            return new JsonResponse(
                [],
                Response::HTTP_GONE
            );
        }
    }

    /**
     * @Route("/{assignment}/reject")
     * @param ShipmentAssignment $assignment
     * @param Request $req
     * @return JsonResponse
     */
    public function rejectAction(ShipmentAssignment $assignment , Request $req)
    {
        $isAssignTimeExpire = $this->get("app.shipment_assignment")
            ->isExpiredAssignTime($assignment);
        if($isAssignTimeExpire) {
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
                Response::HTTP_GONE
            );
        }
    }
}