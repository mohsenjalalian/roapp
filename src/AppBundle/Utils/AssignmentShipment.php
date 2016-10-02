<?php

namespace AppBundle\Utils;

use AppBundle\Entity\AssignmentRequest;
use AppBundle\Entity\Driver;
use AppBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Shipment;

class AssignmentShipment
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    
    public function __construct(EntityManager $entityManager) 
    {
        $this->entityManager = $entityManager;
    }
    // check shipment rejected by some driver or no
    public function filterDriverAction(Shipment $shipment)
    {
        $filterDriver = $this->entityManager
            ->getRepository("AppBundle:AssignmentRequest")
            ->findBy(
                [
                    'shipment' => $shipment->getId(),
                    'status' => 0,
                ]
            );
        if ($filterDriver) {
            foreach ($filterDriver as $value) {
                $banDriver[] = $value->getDriver()->getId();
            }
            return $banDriver;
        } else {
            return $banDriver=[];
        }
    }
    public function waitingStageAction(Shipment $shipment,Driver $driver){
        $em = $this->entityManager;
        // send request to driver
        $sendRequestTime = (int)date("i",time());
        $expireRequestTime = $sendRequestTime+1;
        // waiting for driver answer
        $driver->setIsActive(0); // driver status = waiting
        $shipment->setStatus(4); // shipment status = waiting
        $em->persist($driver);
        $em->persist($shipment);
        $em->flush();
        return $expireRequestTime;
    }
    public function timeOutStageAction(Shipment $shipment,Driver $driver)
    {
        $assignmentRequestObj = new AssignmentRequest();
        $em = $this->entityManager;
        $driver->setIsActive(1); // driver status = free
        $shipment->setStatus(3); // shipment status = timeOut
        $assignmentRequestObj->setShipment($shipment);
        $assignmentRequestObj->setDriver($driver);
        $assignmentRequestObj->setStatus(2); // assignment status = timeOut
        $assignmentRequestObj->setReason("timeOut");
        $em->persist($shipment);
        $em->persist($driver);
        $em->persist($assignmentRequestObj);
        $em->flush();
    }
    public function acceptRequestStageAction(Shipment $shipment,Driver $driver)
    {
        $assignmentRequestObj = new AssignmentRequest();
        $em = $this->entityManager;
        $driver->setIsActive(false); // driver status = busy
        $shipment->setStatus(1); // shipment status = assignment ok
        $assignmentRequestObj->setShipment($shipment);
        $assignmentRequestObj->setDriver($driver);
        $assignmentRequestObj->setStatus(1); // assignment status = accepted
        $assignmentRequestObj->setReason("accepted");
        // create two tasks with diffrent types
        $taskTypes = ['pickup','deliver'];
        foreach ($taskTypes as $key=>$type) {
            $taskTblObj = new Task();
            $taskTblObj->setShipment($shipment);
            $taskTblObj->setStatus(1);
            $taskTblObj->setType($key);
            $taskEm = $this->entityManager;
            $taskEm->persist($taskTblObj);
            $taskEm->flush();
        }
        $em->persist($driver);
        $em->persist($shipment);
        $em->persist($assignmentRequestObj);
        $em->flush();
    }
    public function rejectRequestStageAction(Shipment $shipment,Driver $driver)
    {
        $assignmentRequestObj = new AssignmentRequest();
        $em = $this->entityManager;
        $assignmentRequestObj->setShipment($shipment);
        $assignmentRequestObj->setDriver($driver);
        $assignmentRequestObj->setStatus(0); // assign status = rejected
        $assignmentRequestObj->setReason("نداشتن وقت آزاد");
        $shipment->setStatus(2); // shipment status = rejected by driver
        $em->persist($assignmentRequestObj);
        $em->persist($shipment);
        $em->flush();
    }
}