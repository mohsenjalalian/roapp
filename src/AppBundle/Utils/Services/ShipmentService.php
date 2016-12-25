<?php

namespace AppBundle\Utils\Services;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Shipment;
use AppBundle\Utils\AssignmentShipment;
use AppBundle\Utils\Shipment\ShipmentProcessInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use r;

/**
 * Class PaymentService
 * @package AppBundle\Utils\Services
 */
class ShipmentService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var ShipmentProcessInterface[]
     */
    private $shipments;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var AssignmentShipment
     */
    private $assignmentShipment;

    /**
     * PaymentService constructor.
     * @param EntityManager      $entityManager
     * @param Router             $router
     * @param TokenStorage       $tokenStorage
     * @param AssignmentShipment $assignmentShipment
     */
    public function __construct(EntityManager $entityManager, Router $router, TokenStorage $tokenStorage, AssignmentShipment $assignmentShipment)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->assignmentShipment = $assignmentShipment;
    }
    /**
     * @param Shipment $shipment
     * @param Request  $request
     * @param Form     $form
     *
     */
    public function create(Shipment $shipment, Request $request, $form)
    {
        /** @var Customer $customer */

        $shipmentPrice = $request
            ->request->get('price_shipment');

        $createdAt = new \DateTime();
        $shipment->setPrice(floatval($shipmentPrice));
        $shipment->setCreatedAt($createdAt);
        $shipment->setStatus(Shipment::STATUS_NOT_ASSIGNED);
        $em = $this->entityManager;
        $em->persist($shipment);
        $em->flush();

        $conn = r\connect('localhost', '28015', 'roapp', '09126354397');
        $driverToken = uniqid();
        $trackingToken = uniqid();
        $document = [
            'shipment_id' => $shipment->getId(),
            'driver_token' => $driverToken,
            'tracking_token' => $trackingToken,
            'status'    =>  'disabled',
        ];
        r\table("shipment")
            ->insert($document)
            ->run($conn);
    }

    /**
     *
     */
    public function read()
    {
    }

    /**
     *
     */
    public function readAll()
    {
    }

    /**
     *
     */
    public function edit()
    {
    }

    /**
     * @param string                   $shipmentServiceName
     * @param ShipmentProcessInterface $shipment
     */
    public function addShipment($shipmentServiceName, ShipmentProcessInterface $shipment)
    {
        $this->shipments[$shipmentServiceName] = $shipment;
    }

    /**
     * @return Shipment $shipment
     */
    public function shipmentFactory()
    {
        /** @var Customer $customer */
        $customer = $this->tokenStorage->getToken()->getUser();
        $nameSpace = $customer->getBusinessUnit()->getBusinessType()->getEntityNamespace();
        $shipment = new $nameSpace();
        $shipment = $this->shipmentInit($shipment);

        return $shipment;
    }

    /**
     * @return string
     */
    public function getShipmentFormNamespace()
    {
        /** @var Customer $customer */
        $customer = $this->tokenStorage->getToken()->getUser();
        $nameSpace = $customer->getBusinessUnit()->getBusinessType()->getFormNamespace();

        return $nameSpace;
    }

    /**
     * @param Shipment $shipment
     * @return Shipment $shipment
     */
    public function shipmentInit(Shipment $shipment)
    {
        $customer = $this->tokenStorage->getToken()->getUser();
        $ownerAddress = $customer->getBusinessUnit()->getDefaultAddress();
        $shipment->setOwnerAddress($ownerAddress);
        $em = $this->entityManager;
        $em->persist($ownerAddress);
        $em->flush();

        return $shipment;
    }

    /**
     * @param Shipment $shipment
     * @param integer  $driverId
     * @return bool
     */
    public function shipmentAssign(Shipment $shipment, $driverId)
    {
        $em = $this->entityManager;
        $driver = $em->getRepository('AppBundle:Driver')
            ->find($driverId);
        $this->assignmentShipment->sendRequest($shipment, $driver);

        return true;
    }
}
