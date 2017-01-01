<?php

namespace AppBundle\Utils;

use AppBundle\Entity\ShipmentAssignment;
use AppBundle\Entity\Driver;
use AppBundle\Entity\ShipmentHistory;
use AppBundle\Entity\Task;
use AppBundle\Utils\Services\ShipmentService;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Shipment;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AssignmentShipment
 * @package AppBundle\Utils
 */
class AssignmentShipment
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \AppBundle\Utils\NotificationService
     */
    private $notificationService;

    /**
     * TranslatorInterface
     */
    private $translations;

    /**
     * ShipmentService
     */
    private $shipmentService;

    /**
     * AssignmentShipment constructor.
     * @param EntityManager       $entityManager
     * @param NotificationService $notificationService
     * @param TranslatorInterface $translation
     * @param ShipmentService     $shipmentService
     * @internal param NotificationService $NotificationService
     * @internal param NotificationService $sendNotification
     */
    public function __construct(EntityManager $entityManager, NotificationService $notificationService, TranslatorInterface $translation, ShipmentService $shipmentService)
    {
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
        $this->translations = $translation;
        $this->shipmentService = $shipmentService;
    }

    /**
     * @param Shipment $shipment
     * @param Driver   $driver
     * @return bool
     */
    public function sendRequest(Shipment $shipment, Driver $driver)
    {
        $assignmentObj = $this->setWaitingAssign($shipment, $driver);
        $data = $this->initDataForSend($assignmentObj);
        $this->notificationService->sendNotification($data);
        $this->setExpireTime($assignmentObj);

        return true;
    }

    /**
     * @param ShipmentAssignment $assignmentObj
     * @return array
     */
    public function initDataForSend(ShipmentAssignment $assignmentObj)
    {
        $ownerDescription =  $assignmentObj->getShipment()
            ->getOwnerAddress()
            ->getDescription();
        $otherDescription = $assignmentObj->getShipment()
            ->getOtherAddress()
            ->getDescription();
        $shipmentPrice =  $assignmentObj->getShipment()
            ->getPrice();
        $shipmentPickUpTime = $assignmentObj->getShipment()
            ->getPickUpTime()->getTimestamp();
        $shipmentDescription = $assignmentObj->getShipment()
            ->getDescription();
        $driverId = $assignmentObj->getDriver()
            ->getId();
        $personDevice = $this->entityManager
            ->getRepository('AppBundle:PersonDevice')
            ->findBy(['person' => $driverId]);
        $registerId = $personDevice[0]->getNotificationToken();
        $data =
            [
                'registerId' => $registerId,
                'parameters' => [
                    'assignmentId' => $assignmentObj->getId(),
                    'sourceAddress' => $ownerDescription,
                    'destinationAddress' => $otherDescription,
                    'shipmentPrice' => $shipmentPrice,
                    'shipmentPickUpTime' => $shipmentPickUpTime,
                    'shipmentDescription' => $shipmentDescription,
                ],
            ];

        return $data;
    }

    /**
     * @param Shipment $shipment
     * @param Driver   $driver
     * @return ShipmentAssignment
     */
    public function setWaitingAssign(Shipment $shipment, Driver $driver)
    {
        $shipmentAssignmentObj = new ShipmentAssignment();
        $em = $this->entityManager;
        // waiting for driver answer
        $driver->setStatus(Driver::STATUS_IN_PROGRESS); // driver status = waiting
        $shipment->setStatus(Shipment::STATUS_ASSIGNMENT_SENT); // shipment status = waiting
        $shipmentAssignmentObj->setShipment($shipment);
        $shipmentAssignmentObj->setDriver($driver);
        $shipmentAssignmentObj
            ->setReason(
                $this->translations->trans("waiting")
            );
        $shipmentAssignmentObj->setStatus(ShipmentAssignment::STATUS_WAITING);

        $em->persist($driver);
        $em->persist($shipment);
        $em->persist($shipmentAssignmentObj);
        $assignmentId = $shipmentAssignmentObj;
        $em->flush();

        $this->shipmentService->addHistory($shipment, ShipmentHistory::ACTION_SEND_ASSIGNMENT);

        return $assignmentId;
    }

    /**
     * @param ShipmentAssignment $assignment
     */
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

    /**
     * @param ShipmentAssignment $assignment
     */
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
            ->setReason(
                $this->translations->trans("time over")
            );

        $em->persist($assignment);
        $em->flush();

        $this->shipmentService->addHistory($assignment->getShipment(), ShipmentHistory::ACTION_TIMEOUT);
    }

    /**
     * @param ShipmentAssignment $assignment
     * @return bool
     */
    public function isExpiredAssignTime(ShipmentAssignment $assignment)
    {
        $currentTime = new \DateTime();
        $expireTime = $assignment->getExpireTime();
        if ($currentTime > $expireTime) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param ShipmentAssignment $assignment
     */
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
            ->setReason(
                $this->translations->trans("accept shipment")
            );
        $assignment
            ->setDriverExchangeCode($this->generateExchangeCode());
        $assignment
            ->setReciverExchangeCode($this->generateExchangeCode());
        // create two tasks with diffrent types
        $this->createTasks($assignment->getShipment());

        $em->persist($assignment);
        $em->flush();

        $this->shipmentService->addHistory($assignment->getShipment(), ShipmentHistory::ACTION_PICKUP);
    }

    /**
     * @param shipment $shipment
     */
    public function createTasks($shipment)
    {
        $em = $this->entityManager;
        $taskTypes = ['pickup', 'deliver'];
        foreach ($taskTypes as $key => $type) {
            $taskTblObj = new Task();
            $taskTblObj->setShipment($shipment);
            $taskTblObj->setStatus(1);
            $taskTblObj->setType($key);

            $em->persist($taskTblObj);
            $em->flush();
        }
    }

    /**
     * @param ShipmentAssignment $assignment
     * @param string             $reason
     */
    public function rejectRequest(ShipmentAssignment $assignment, $reason)
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

        $this->shipmentService->addHistory($assignment->getShipment(), ShipmentHistory::ACTION_REJECT_BY_DRIVER);
    }

    /**
     * @param int $length
     * @return int
     */
    public function generateExchangeCode($length = 6)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomCode = '';
        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[rand(0, $charactersLength - 1)];
        }

        return intval($randomCode);
    }
}
