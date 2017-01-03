<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\Entity\ShipmentAssignment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use r;

/**
 * Class AssignmentShipmentController
 * @package AppBundle\Controller\Driver\Api\V1
 * @Route("/shipment_assignment")
 */
class ShipmentAssignmentController extends Controller
{

    /**
     * @Route("/{assignment}/accept")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @param ShipmentAssignment $assignment
     * @return JsonResponse
     */
    public function acceptAction(ShipmentAssignment $assignment)
    {
        $isAssignTimeExpire = $this->get("app.shipment_assignment")
            ->isExpiredAssignTime($assignment);
        if ($isAssignTimeExpire) {
            $this->get('app.shipment_assignment')
                ->acceptRequest($assignment);
//            $driverExchangeCode = $assignment->getDriverExchangeCode();
            $param = $this->prepareAcceptInfo($assignment);

            return new JsonResponse(
                [
                    $param,
                ]
            );
        } else {
            $conn = r\connect(
                $this->getParameter('rethinkdb_host'),
                $this->getParameter('rethinkdb_port'),
                'roapp',
                $this->getParameter('rethink_password')
            );
            $result = r\table('shipment')
                ->filter(
                    [
                        'shipment_id' => $assignment->getShipment()->getId(),
                    ]
                )
                ->run($conn);
            /** @var \ArrayObject $current */
            $current = $result->current();
            $driverToken = $current->getArrayCopy()['driver_token'];
            $this->get("app.shipment_assignment")
                ->timeOutAction($assignment);

            return new JsonResponse(
                [
                    'driver_token' => $driverToken,
                ],
                Response::HTTP_GONE
            );
        }
    }

    /**
     * @Route("/{assignment}/reject")
     * @Method("POST")
     * @param ShipmentAssignment $assignment
     * @param  Request            $req
     * @return JsonResponse
     */
    public function rejectAction(ShipmentAssignment $assignment, Request $req)
    {
        $isAssignTimeExpire = $this->get("app.shipment_assignment")
            ->isExpiredAssignTime($assignment);
        if ($isAssignTimeExpire) {
            $reason = $req->getContent();
            $reason = json_decode($reason);
            $this->get("app.shipment_assignment")
                ->rejectRequest($assignment, $reason->reason);

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
     * @param ShipmentAssignment $assignment
     * @return array
     */
    public function prepareAcceptInfo(ShipmentAssignment $assignment)
    {
        $ownerLatitude = $assignment->getShipment()
            ->getOwnerAddress()
            ->getLatitude();
        $ownerLongitude = $assignment->getShipment()
            ->getOwnerAddress()
            ->getLongitude();
        $ownerDescription =  $assignment->getShipment()
            ->getOwnerAddress()
            ->getDescription();
        $otherLatitude = $assignment->getShipment()
            ->getOtherAddress()
            ->getLatitude();
        $otherLongitude = $assignment->getShipment()
            ->getOtherAddress()
            ->getLongitude();
        $otherDescription = $assignment->getShipment()
            ->getOtherAddress()
            ->getDescription();
        $driverExchangeCode = $assignment->getDriverExchangeCode();
        $shipmentPickUpTime = $assignment->getShipment()
            ->getPickUpTime()
            ->getTimestamp();
        $shipmentDescription = $assignment->getShipment()
            ->getDescription();
        $shipmentPrice = $assignment->getShipment()
            ->getPrice();
        $shipmentId = $assignment->getShipment()
            ->getId();
        $senderName = $assignment->getShipment()
            ->getOwnerAddress()
            ->getCustomer()
            ->getFullName();
        $senderPhone = $assignment->getShipment()
            ->getOwnerAddress()
            ->getCustomer()
            ->getPhone();
        $reciverName = $assignment->getShipment()
            ->getOther()
            ->getFullName();
        $reciverPhone = $assignment->getShipment()
            ->getOther()
            ->getPhone();
        $shipmentValue = $assignment->getShipment()
            ->getValue();
        $shipmentPhotos = $assignment->getShipment()
            ->getPhotoFiles();
        $photoUrl = [];
        if ($shipmentPhotos) {
            foreach ($shipmentPhotos as $value) {
                $photoUrl[] = $this->get("roapp_media.upload_manager")
                   ->generateAbsoluteUrl($value->getMediaEntity());
            }
        } else {
            $photoUrl = null;
        }
        $parameters = [
            'ownerLatitude' => $ownerLatitude,
            'ownerLongitude' => $ownerLongitude,
            'ownerDescription' => $ownerDescription,
            'otherLatitude' => $otherLatitude,
            'otherLongitude' => $otherLongitude,
            'otherDescription' => $otherDescription,
            'driverExchangeCode' => $driverExchangeCode,
            'shipmentPickUpTime' => $shipmentPickUpTime,
            'shipmentPrice' => $shipmentPrice,
            'shipmentId' => $shipmentId,
            'senderName' => $senderName,
            'senderPhone' => $senderPhone,
            'reciverName' => $reciverName,
            'reciverPhone' => $reciverPhone,
            'shipmentValue' => $shipmentValue,
            'shipmentPhoto' => $photoUrl,
            'shipmentDescription' => $shipmentDescription,
        ];

         return $parameters;
    }
}
