<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\Entity\Driver;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentAssignment;
use AppBundle\Entity\ShipmentHistory;
use AppBundle\Utils\AssignmentShipment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @param Request $request
     * @return JsonResponse
     * driver can fail shipment
     */
    public function fail(Request $request)
    {
        $data = json_decode($request->getContent());
        $em = $this->getDoctrine()->getManager();
        $assignment = $this->getDoctrine()
            ->getRepository("AppBundle:ShipmentAssignment")
            ->find($data->assignmentId);
        if ($assignment) {
            $assignment->setStatus(ShipmentAssignment::STATUS_DRIVER_FAILED);
            $assignment->getShipment()->setStatus(Shipment::STATUS_DRIVER_FAILED);
            $assignment->getDriver()->setStatus(Driver::STATUS_FREE);

            $em->persist($assignment);
            $em->flush();

//            $this->get('app.shipment_service')->addHistory($shipment, ShipmentHistory::ACTION_FAIL_BY_DRIVER);

            // send sms to customer
            $sendNotification = $this->get("logger");
            $sendNotification->info("your shipment failed By driver");

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } else {
            return new JsonResponse(
                [],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/validation_code")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @param Request $request
     * @return JsonResponse
     */
    public function validateCode(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $assignmentId = $data->assignmentId;
        $receiverCode = $data->receiverCode;
        $assignment = $this->getDoctrine()
            ->getRepository("AppBundle:ShipmentAssignment")
            ->find($assignmentId);
        if ($assignment->getReciverExchangeCode() == $receiverCode) {
            $assignment->getShipment()
                ->setStatus(Shipment::STATUS_DELIVERED);
            $em->persist($assignment);

            $em->flush();

            $this->get('app.shipment_service')->addHistory($assignment->getShipment(), ShipmentHistory::ACTION_DELIVER);

            // send sms to sender customer
            $logger = $this->get("logger");
            $logger->info("your package deliver to destination");

            return new JsonResponse(
                [],
                Response::HTTP_OK
            );
        } else {
            return new JsonResponse(
                [],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/change_status")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @param Request $request
     * @return JsonResponse|void
     */
    public function changeStatus(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());
        $shipmentId = $data->shipmentId;
        $shipment = $this->getDoctrine()
            ->getRepository("AppBundle:Shipment")
            ->find($shipmentId);
        if ($shipment) {
            switch ($data->status) {
                case 1: // status = on pick up
                    $shipment->setStatus(Shipment::STATUS_ON_PICK_UP);
                    $this->get('app.shipment_service')->addHistory($shipment, ShipmentHistory::ACTION_START_PICKUP);
                    break;
                case 2: // status =  on delivery
                    $shipment->setStatus(Shipment::STATUS_ON_DELIVERY);
                    $this->get('app.shipment_service')->addHistory($shipment, ShipmentHistory::ACTION_START_DELIVERY);
                    break;
                case 3: // status = finish
                    $shipment->setStatus(Shipment::STATUS_FINISH);
                    $this->get('app.shipment_service')->addHistory($shipment, ShipmentHistory::ACTION_DELIVER);
                    $driverId = $this->getUser()
                        ->getId();
                    $driver = $this->getDoctrine()
                        ->getRepository("AppBundle:Driver")
                        ->find($driverId);
                    if ($driver instanceof Driver) {
                        $driver->setStatus(Driver::STATUS_FREE);
                    } else {
                        return new JsonResponse(
                            [],
                            Response::HTTP_BAD_REQUEST
                        );
                    }
                    break;
            }
            $em->persist($shipment);

            $em->flush();

            return new JsonResponse(
                [],
                Response::HTTP_NO_CONTENT
            );
        } else {
            return new JsonResponse(
                [],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/get_task_status")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @param Request $request
     * @return Response
     */
    public function getTaskStatus(Request $request)
    {
        $data = json_decode($request->getContent());
        $shipmentId = $data->shipmentId;
        $em = $this->getDoctrine()->getEntityManager();
        $shipment = $em->getRepository("AppBundle:Shipment")->find($shipmentId);
        $status = $shipment->getStatus();
        if ($status == Shipment::STATUS_CUSTOMER_FAILED) {
            $response = 'customer_failed';
        } elseif ($status == Shipment::STATUS_DRIVER_FAILED) {
            $response = 'driver_failed';
        } elseif ($status == Shipment::STATUS_ON_PICK_UP) {
            $response = 'on_pick_up';
        } elseif ($status == Shipment::STATUS_PICKED_UP || $status == Shipment::STATUS_ON_DELIVERY) {
            $response = 'on_delivery';
        } elseif ($status == Shipment::STATUS_DELIVERED || $status == Shipment::STATUS_FINISH) {
            $response = 'finish';
        } else {
            $response = 'no_state';
        }

        return new Response($response, Response::HTTP_OK);
    }
}
