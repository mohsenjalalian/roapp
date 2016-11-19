<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\Entity\Driver;
use AppBundle\Entity\Shipment;
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
        $reciverCode = $data->reciverCode;
        $assignment = $this->getDoctrine()
            ->getRepository("AppBundle:ShipmentAssignment")
            ->find($assignmentId);
        if ($assignment->getReciverExchangeCode() == $reciverCode) {
            $assignment->getShipment()
                ->setStatus(Shipment::STATUS_DELIVERED);
            $em->persist($assignment);

            $em->flush();

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
        $assignmentId = $data->assignmentId;
        $assignment = $this->getDoctrine()
            ->getRepository("AppBundle:ShipmentAssignment")
            ->find($assignmentId);
        if ($assignment) {
            switch ($data->status) {
                case 1 : // status = on pick up
                    $assignment->getShipment()
                        ->setStatus(Shipment::STATUS_ON_PICK_UP);
                    break;
                case 2 : // status =  on delivery
                    $assignment->getShipment()
                        ->setStatus(Shipment::STATUS_ON_DELIVERY);
                    break;
                case 3 : // status = finish
                    $assignment->getShipment()
                        ->setStatus(Shipment::STATUS_FINISH);
                    $assignment->getDriver()
                        ->setStatus(Driver::STATUS_FREE);
                    break;
            }
            $em->persist($assignment);

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
}