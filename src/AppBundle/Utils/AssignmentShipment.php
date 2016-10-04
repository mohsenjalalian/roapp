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
                $banDriver[] = $value->getDriver()
                    ->getId();
            }

            return $banDriver;
        } else {
            return $banDriver=[];
        }
    }
    public function sendRequest(Shipment $shipment,Driver $driver)
    {
        $assignmentId = $this->setWaitingAssign($shipment,$driver);
        $this->sendNotification();
        $this->setExpireTime($assignmentId);
        
        return false;
    }
    public function setWaitingAssign(Shipment $shipment , Driver $driver)
    {
        $assignmentRequestObj = new AssignmentRequest();
        $em = $this->entityManager;
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
        $assignmentId = $assignmentRequestObj->getId();
        
        $em->flush();
        
        return $assignmentId;
    }
    public function sendNotification()
    {
        $sendUrl = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization:key = AIzaSyBa77ogNwwV0R8hDPatYRGy_y1IsBlCTIY',
            'Content-Type: application/json'
        );
        $title='درخواست تحویل سفارش';
        $text= "سفارش با مشخصات زیر آماده ارسال می باشد";
        $topic='sharzh';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sendUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $fields = array(
            'notification' => array(
                'title' => $title,
                'body' => $text
            ),
            'to' => '/topics/'.$topic
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result===FALSE) {
            die("curl deny:" . curl_error($ch));
        }
        curl_close($ch);
        // send request to driver

        return true;
    }
    public function setExpireTime($assignmentId)
    {
        $em = $this->entityManager;
        $assignmentTbl = $em->getRepository("AppBundle:AssignmentRequest")
            ->find($assignmentId);
        $crateAssignTime = $assignmentTbl->getDateTime()
            ->format("Y-m-d H:i:s");
        $currentTime = strtotime($crateAssignTime);
        $currentTime = $currentTime+(60*10);
        $currentTime = date("Y-m-d H:i:s", $currentTime);
        $expireRequestTime = new \DateTime($currentTime, new \DateTimeZone('Asia/Tehran'));
        $assignmentTbl->setExpireTime($expireRequestTime);
        $em->persist($assignmentTbl);
        
        $em->flush();
        
        return true;
    }
    public function timeOutAction($shipmentId,$driverId)
    {
        $em = $this->entityManager;
        $driverObj = $em->getRepository("AppBundle:Driver")
            ->find($driverId);
        $shipmentObj = $em->getRepository("AppBundle:Shipment")
            ->find($shipmentId);
        $driverObj->setStatus(Driver::STATUS_FREE); // driver status = free
        $shipmentObj->setStatus(Shipment::STATUS_NOT_ASSIGNED);
        $currentAssignObj = $this->fetchCurrentAssignmentInfo($shipmentId,$driverId);
        $currentAssignObj->setStatus(AssignmentRequest::STATUS_TIMEOUT); // assign status = timeOut
        $currentAssignObj->setReason("time over");
        $em->persist($shipmentObj);
        $em->persist($driverObj);
        $em->persist($currentAssignObj);
        
        $em->flush();
        
        return true;
    }
    public function checkAssignTime($shipmentId,$driverId)
    {
        $currentAssignObj = $this->fetchCurrentAssignmentInfo($shipmentId,$driverId);
        $currentTime = new \DateTime();
        $expireTime = $currentAssignObj->getExpireTime();
        if ($currentTime <= $expireTime) {
            return true;
        } else {
            return false;
        }
    }
    public function acceptRequest($shipmentId,$driverId)
    {
        $em = $this->entityManager;
        $driverObj = $em->getRepository("AppBundle:Driver")
            ->find($driverId);
        $shipmentObj = $em->getRepository("AppBundle:Shipment")
            ->find($shipmentId);
        $driverObj->setStatus(Driver::STATUS_BUSY); // driver status = busy
        $shipmentObj->setStatus(Shipment::STATUS_ASSIGNED); // shipment status = assignment ok
        $currentAssignObj = $this->fetchCurrentAssignmentInfo($shipmentId,$driverId);
        $currentAssignObj->setStatus(AssignmentRequest::STATUS_ACCEPTED);
        $currentAssignObj->setReason("Accept shipment");
        // create two tasks with diffrent types
        $this->createTasks($shipmentId);
        $em->persist($driverObj);
        $em->persist($shipmentObj);
        $em->persist($currentAssignObj);
        
        $em->flush();
        
        return true;
    }
    public function createTasks($shipmentId)
    {
        $em = $this->entityManager;
        $shipment = $em->getRepository("AppBundle:Shipment")
            ->find($shipmentId);
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
        
        return true;
    }
    public function rejectRequest($shipmentId,$driverId,$reason)
    {
        $em = $this->entityManager;
        $driverObj = $em->getRepository("AppBundle:Driver")
            ->find($driverId);
        $shipmentObj = $em->getRepository("AppBundle:Shipment")
            ->find($shipmentId);
        $driverObj->setStatus(Driver::STATUS_FREE); // driver status = busy
        $shipmentObj->setStatus(Shipment::STATUS_NOT_ASSIGNED);
        $currentAssignObj = $this->fetchCurrentAssignmentInfo($shipmentId,$driverId);
        $currentAssignObj->setStatus(AssignmentRequest::STATUS_REJECTED); // assign status = rejected
        $currentAssignObj->setReason($reason);
        $em->persist($currentAssignObj);
        $em->persist($shipmentObj);
        $em->persist($driverObj);

        $em->flush();
        
        return true;
    }
    public function fetchCurrentAssignmentInfo($shipmentId,$driverId)
    {
        $em = $this->entityManager;
        $qb = $em->createQueryBuilder();
        $currentAssignResult = $qb->select('a')
            ->from('AppBundle:AssignmentRequest','a')
            ->where('a.shipment=:shipmentId')
            ->andWhere('a.driver=:driverId')
            ->andWhere('a.status=:assignmentStatus')
            ->setParameter('shipmentId',$shipmentId)
            ->setParameter('driverId',$driverId)
            ->setParameter('assignmentStatus',AssignmentRequest::STATUS_WAITING)
            ->getQuery()
            ->getSingleResult();
        
        return $currentAssignResult;
    }
}