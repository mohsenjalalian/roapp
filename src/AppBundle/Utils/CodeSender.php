<?php

namespace AppBundle\Utils;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Driver;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentAssignment;
use AppBundle\Utils\Services\NotificationService;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * class CodeSender
 */
class CodeSender
{
    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * @var Sms
     */
    private $smsService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * CodeSender constructor.
     * 
     * @param NotificationService     $notificationService Notification service
     * @param Sms                     $smsService          SMS service
     * @param TranslatorInterface     $translator          Translator
     */
    public function __construct($notificationService, $smsService, $translator)
    {
        $this->notificationService = $notificationService;
        $this->smsService = $smsService;
        $this->translator = $translator;
    }

    /**
     * Sends codes to driver and receiver
     * 
     * @param Shipment $shipment Shipment entity
     */
    public function send($shipment)
    {
        
        $shipment->getAssignments()
            ->containsKey('status', ShipmentAssignment::STATUS_ACCEPTED);
        /**
         * @var Driver
         */
        $driver = new Driver();
        /**
         * @var Customer
         */
        $receiver = new Customer();
        
        $driverCode = 0;
        $receiverCode = 0;
        
        // Send sms
        $this->smsService->send(
            $driver->getPhone(),
            $this->translator->trans('Please give this code to consignment sender:') . $driverCode
        );
        $this->smsService->send(
            $receiver->getPhone(),
            $this->translator->trans('Please give this code to driver:') . $receiverCode
        );
        
        // Send notification
    }
}