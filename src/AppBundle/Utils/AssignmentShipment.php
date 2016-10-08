<?php

namespace AppBundle\Utils;

use AppBundle\Entity\ShipmentAssignment;
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

    /**
     * @var SendNotification
     */
    private $sendNotification;

    public function __construct(EntityManager $entityManager , SendNotification $sendNotification)
    {
        $this->entityManager = $entityManager;
        $this->sendNotification = $sendNotification;
    }
    public function sendRequest(Shipment $shipment,Driver $driver)
    {
        $assignmentObj = $this->setWaitingAssign($shipment,$driver);
        $data = $this->initDataForSend($assignmentObj);
        $this->sendNotification->sendNotification($data);
        $this->setExpireTime($assignmentObj);

        return true;
    }
    public function initDataForSend(ShipmentAssignment $assignmentObj)
    {
        $data =
            [
                'title'=>'درخواست تحویل سفارش',
                'body'=> 'سفارش با مشخصات زیر آماده ارسال می باشد' ,
                'topic'=>'charge',
                'parameters'=>
                    [
                        'assignmentId'=>$assignmentObj->getId(),
                        'type' => 'test'
                    ]
            ];
        
        return $data;
    }
    public function setWaitingAssign(Shipment $shipment , Driver $driver)
    {
        $ShipmentAssignmentObj = new ShipmentAssignment();
        $em = $this->entityManager;
        // waiting for driver answer
        $driver->setStatus(Driver::STATUS_IN_PROGRESS); // driver status = waiting
        $shipment->setStatus(Shipment::STATUS_ASSIGNMENT_SENT); // shipment status = waiting
        $ShipmentAssignmentObj->setShipment($shipment);
        $ShipmentAssignmentObj->setDriver($driver);
        $ShipmentAssignmentObj->setReason('waiting');
        $ShipmentAssignmentObj->setStatus(ShipmentAssignment::STATUS_WAITING);
        $em->persist($driver);
        $em->persist($shipment);
        $em->persist($ShipmentAssignmentObj);
        $assignmentId = $ShipmentAssignmentObj;

        $em->flush();

        return $assignmentId;
    }
    public function setExpireTime(ShipmentAssignment $assignment)
    {
        $em = $this->entityManager;

        $crateAssignTime = $assignment
            ->getDateTime()
            ->format("Y-m-d H:i:s");
        $currentTime = strtotime($crateAssignTime);
        $currentTime = $currentTime+(60*10);
        $currentTime = date("Y-m-d H:i:s", $currentTime);
        $expireRequestTime = new \DateTime($currentTime, new \DateTimeZone('Asia/Tehran'));
        $assignment->setExpireTime($expireRequestTime);
        $em->persist($assignment);

        $em->flush();
        
    }
    public function timeOutAction(ShipmentAssignment $assignment)
    {
        $em = $this->entityManager;
        $assignment
            ->getDriver()
            ->setStatus(Driver::STATUS_FREE);
        $assignment
            ->getShipment()
            ->setStatus(Shipment::STATUS_NOT_ASSIGNED);
        $assignment
            ->setStatus(ShipmentAssignment::STATUS_TIMEOUT);
        $assignment
            ->setReason("time over");

        $em->persist($assignment);

        $em->flush();
        
    }
    public function isAssignTimeExpire(ShipmentAssignment $assignment)
    {
        $currentTime = new \DateTime();
        $expireTime = $assignment->getExpireTime();
        if ($currentTime <= $expireTime) {
            return false;
        } else {
            return true;
        }
    }
    public function acceptRequest(ShipmentAssignment $assignment)
    {
        $em = $this->entityManager;
        $assignment
            ->getShipment()
            ->setStatus(Shipment::STATUS_ASSIGNED);
        $assignment
            ->getDriver()
            ->setStatus(Driver::STATUS_BUSY);
        $assignment
            ->setStatus(ShipmentAssignment::STATUS_ACCEPTED);
        $assignment
            ->setReason("Accept shipment");
        // create two tasks with diffrent types
        $this->createTasks($assignment->getShipment());

        $em->persist($assignment);

        $em->flush();
        
    }
    public function createTasks($shipment)
    {
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
    }
    public function rejectRequest(ShipmentAssignment $assignment , $reason)
    {
        $em = $this->entityManager;
        $assignment
            ->getDriver()
            ->setStatus(Driver::STATUS_FREE);
        $assignment
            ->getShipment()
            ->setStatus(Shipment::STATUS_NOT_ASSIGNED);
        $assignment
            ->setStatus(ShipmentAssignment::STATUS_REJECTED);
        $assignment
            ->setReason($reason);
        
        $em->persist($assignment);

        $em->flush();
    }
}