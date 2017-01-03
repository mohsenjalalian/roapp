<?php

namespace AppBundle\Utils\Services;

use AppBundle\Entity\AbstractInvoice;
use AppBundle\Entity\Customer;
use AppBundle\Entity\PeriodInvoice;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentHistory;
use AppBundle\Exception\ShipmentException;
use AppBundle\Utils\Shipment\ShipmentProcessInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use r;

/**
 * Class PaymentService
 * @package AppBundle\Utils\Services
 */
class ShipmentService
{
    /**
     * @var ShipmentProcessInterface[]
     */
    private $shipments;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * PaymentService constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param Shipment $shipment
     * @param Request  $request
     */
    public function create(Shipment $shipment, Request $request)
    {
        /** @var Customer $customer */

        $shipmentPrice = $request
            ->request->get('price_shipment');

        $createdAt = new \DateTime();
        $shipment->setPrice(floatval($shipmentPrice));
        $shipment->setCreatedAt($createdAt);
        $shipment->setStatus(Shipment::STATUS_NOT_ASSIGNED);
        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->persist($shipment);
        $em->flush();

        $isBusinessUnitDriver = $shipment->getIsBusinessUnitDriver();
        if (!$isBusinessUnitDriver) {
            $this->createInvoice($shipment);
        }

        $this->addHistory($shipment, ShipmentHistory::ACTION_CREATE);

        $conn = r\connect(
            $this->container->getParameter('rethinkdb_host'),
            $this->container->getParameter('rethinkdb_port'),
            'roapp',
            $this->container->getParameter('rethink_password')
        );
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
        $customer = $this->container->get('security.token_storage')->getToken()->getUser();
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
        $customer = $this->container->get('security.token_storage')->getToken()->getUser();
        $nameSpace = $customer->getBusinessUnit()->getBusinessType()->getFormNamespace();

        return $nameSpace;
    }

    /**
     * @param Shipment $shipment
     * @return Shipment $shipment
     */
    public function shipmentInit(Shipment $shipment)
    {
        $customer = $this->container->get('security.token_storage')->getToken()->getUser();
        $ownerAddress = $customer->getBusinessUnit()->getDefaultAddress();
        if ($ownerAddress == null) {
            throw new ShipmentException();
        }
        $shipment->setOwnerAddress($ownerAddress);
        $em = $this->container->get('doctrine.orm.entity_manager');
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
        $em = $this->container->get('doctrine.orm.entity_manager');
        $driver = $em->getRepository('AppBundle:Driver')
            ->find($driverId);
        $this->container->get('app.shipment_assignment')->sendRequest($shipment, $driver);

        return true;
    }

    /**
     * @param Shipment $shipment
     * @param integer  $action
     */
    public function addHistory(Shipment $shipment, $action)
    {
        $customer = $this->container->get('security.token_storage')->getToken()->getUser();
        $em = $this->container->get('doctrine.orm.entity_manager');

        $shipmentHistory = new ShipmentHistory();
        $shipmentHistory->setAction($action);
        $shipmentHistory->setActor($customer);
        $shipmentHistory->setShipment($shipment);

        $em->persist($shipmentHistory);
        $em->flush($shipmentHistory);
    }

    /**
     * @param Shipment $shipment
     */
    public function createInvoice(Shipment $shipment)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $todayPeriodInvoice = $em->getRepository("AppBundle:PeriodInvoice")->getTodayPeriodInvoice();
        if ($todayPeriodInvoice instanceof PeriodInvoice) {
            $previousPrice = intval($todayPeriodInvoice->getPrice());
            $newPrice = intval($shipment->getPrice());
            $totalPrice = $previousPrice + $newPrice;
            $todayPeriodInvoice->setPrice($totalPrice);
            $todayPeriodInvoice->addShipment($shipment);

            $em->persist($todayPeriodInvoice);
            $em->flush();
        } else {
            $periodInvoice =  new PeriodInvoice();
            $periodInvoice->setPrice($shipment->getPrice());
            $periodInvoice->setStatus(AbstractInvoice::STATUS_UNPAID);
            $periodInvoice->addShipment($shipment);

            $em->persist($periodInvoice);
            $em->flush();
        }
    }
}
