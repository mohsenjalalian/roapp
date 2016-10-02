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
                    'status' => AssignmentRequest::STATUS_REJECTED,
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
        $assignmentRequestObj = new AssignmentRequest();
        $em = $this->entityManager;
        // send request to driver
        $sendRequestTime = (int)date("i",time());
        $expireRequestTime = $sendRequestTime+1;
        // waiting for driver answer
        $driver->setStatus(Driver::STATUS_IN_PROGRESS); // driver status = waiting
        $shipment->setStatus(Shipment::STATUS_ASSIGNMENT_SENT); // shipment status = waiting
        $assignmentRequestObj->setShipment($shipment);
        $assignmentRequestObj->setDriver($driver);
        $assignmentRequestObj->setReason('waiting');
        $assignmentRequestObj->setStatus(AssignmentRequest::STATUS_WAITING);
        $em->persist($driver);
        $em->persist($shipment);
        $em->persist($assignmentRequestObj);
        $em->flush();
        
        return $expireRequestTime;
    }
    public function timeOutStageAction(Shipment $shipment,Driver $driver)
    {
        $assignmentRequestObj = new AssignmentRequest();
        $em = $this->entityManager;
        $driver->setStatus(Driver::STATUS_FREE); // driver status = free
        $shipment->setStatus(Shipment::STATUS_NOT_ASSIGNED); // shipment status = timeOut
        $assignmentRequestObj->setShipment($shipment);
        $assignmentRequestObj->setDriver($driver);
        $assignmentRequestObj->setStatus(AssignmentRequest::STATUS_TIMEOUT); // assignment status = timeOut
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
        $driver->setStatus(Driver::STATUS_BUSY); // driver status = busy
        $shipment->setStatus(Shipment::STATUS_ASSIGNED); // shipment status = assignment ok
        $assignmentRequestObj->setShipment($shipment);
        $assignmentRequestObj->setDriver($driver);
        $assignmentRequestObj->setStatus(AssignmentRequest::STATUS_ACCEPTED); // assignment status = accepted
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
        $shipment->setStatus(Shipment::STATUS_NOT_ASSIGNED);
        $driver->setStatus(Driver::STATUS_FREE);
        $assignmentRequestObj->setShipment($shipment);
        $assignmentRequestObj->setDriver($driver);
        $assignmentRequestObj->setStatus(AssignmentRequest::STATUS_REJECTED); // assign status = rejected
        $assignmentRequestObj->setReason("نداشتن وقت آزاد");
        $em->persist($assignmentRequestObj);
        $em->persist($shipment);
        $em->persist($driver);

        $em->flush();
    }
}