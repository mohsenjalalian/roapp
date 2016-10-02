<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\AssignmentRequest;
use AppBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\Driver;

class AssignShipmentController extends Controller
{
    /**
     * @Route("/assignShipment",name="operator_dashboard_assignShipment")
     */
    public function indexAction(Request $request)
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM AppBundle:Shipment a ORDER BY a.pickUpTime ";
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        return $this->render(
            "operator/dashboard/assignShipment/index.html.twig",
            [
                'pagination' => $pagination
            ]
        );
    }
    /**
     * @Route("/driverAssign/{shipment}",name="operator_dashboard_driverAssign")
     */
    public function driverAssignAction(Shipment $shipment=null,Request $request)
    {
        // check shipment is exist
        if ($shipment) {
            // check shipment rejected by some driver or no
            $banDriver = $this->get("app.shipment_assignment")
                ->filterDriverAction($shipment);
            $em    = $this->get('doctrine.orm.entity_manager');
            $dql   = "SELECT d FROM AppBundle:Driver d ORDER BY d.username";
            $query = $em->createQuery($dql);
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10/*limit per page*/
            );
            return $this->render(
                "operator/dashboard/assignShipment/driverListAssign.html.twig",
                [
                    'shipmentId'=>$shipment->getId(),
                    'banDriverList'=>$banDriver,
                    'pagination'=>$pagination
                ]
            );
        }
        else {
            return $this->redirectToRoute("operator_dashboard_assignShipment");
        }
    }
    /**
     * @Route("/assignSet/{shipment}/{driver}",name="operator_dashboard_assignSet")
     */
    public function assignSetAction(Shipment $shipment=null,Driver $driver=null)
    {
        // if there is shipment and driver with given id
        if ($shipment && $driver){
            $expireRequestTime = $this->get("app.shipment_assignment")
                ->waitingStageAction($shipment,$driver);
            $driverAnswer = $this->driverAnswerAction();
            // check time.driver answer is expire or no
            if (date("i",time())>$expireRequestTime){
                  $this->get("app.shipment_assignment")
                    ->timeOutStageAction($shipment,$driver);
                return $this->render(
                    "operator/dashboard/assignShipment/timeOutMsg.html.twig",
                    [
                        'shipmentId'=>$shipment->getId()
                    ]
                );
                // if driver accept request
            } else {
                if ($driverAnswer) {
                    $this->get("app.shipment_assignment")
                        ->acceptRequestStageAction($shipment,$driver);
                    return $this->redirectToRoute(
                        "operator_dashboard_assignShipment"
                    );
                    // driver reject request
                } else {
                    $this->get("app.shipment_assignment")
                        ->rejectRequestStageAction($shipment,$driver);
                    return $this->render(
                        ':operator/dashboard/assignShipment:rejectMsg.html.twig',
                        [
                            'shipmentId'=>$shipment->getId(),
                        ]
                    );
                }
            }
        } else{
            return $this->redirectToRoute("operator_dashboard_assignShipment");
        }
    }
    public function driverAnswerAction(){
//        return mt_rand(false,true);
//        sleep(5);
        return false;
    }
}