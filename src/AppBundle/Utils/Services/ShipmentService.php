<?php

namespace AppBundle\Utils\Services;

use AppBundle\Entity\AbstractInvoice;
use AppBundle\Entity\Customer;
use AppBundle\Entity\Driver;
use AppBundle\Entity\PeriodInvoice;
use AppBundle\Entity\Shipment;
use AppBundle\Entity\ShipmentAssignment;
use AppBundle\Entity\ShipmentHistory;
use AppBundle\Exception\ShipmentException;
use AppBundle\Utils\Shipment\ShipmentProcessInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use r;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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
        $trackingToken = uniqid();
        $shipment->setTrackingToken($trackingToken);
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
     * @param Shipment           $shipment
     * @param Request            $request
     * @param int                $oldPrice
     * @param ShipmentAssignment $oldAssignmentShipment
     */
    public function edit(Shipment $shipment, Request $request, $oldPrice, ShipmentAssignment $oldAssignmentShipment = null)
    {
        $shipmentPrice = $request
            ->request->get('price_shipment');

        $shipment->setPrice(floatval($shipmentPrice));
        $shipment->setStatus(Shipment::STATUS_NOT_ASSIGNED);
        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->persist($shipment);

        $isBusinessUnitDriver = $shipment->getIsBusinessUnitDriver();
        if (!$isBusinessUnitDriver) {
            $this->editInvoice($shipment, $oldPrice, false, $oldAssignmentShipment);
        } else {
            $this->editInvoice($shipment, $oldPrice, true, $oldAssignmentShipment);
        }
        $em->flush();
        $this->addHistory($shipment, ShipmentHistory::ACTION_CREATE);
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
     * @throws ShipmentException
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
        $em = $this->container->get('doctrine.orm.entity_manager');
        $shipmentHistory = new ShipmentHistory();
        $token = $this->container->get('security.token_storage')->getToken();
        if ($token instanceof TokenStorage) {
            $shipmentHistory->setActor($token);
        } else {
            $shipmentHistory->setActor(null);
        }
        $shipmentHistory->setAction($action);
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

    /**
     * @param Shipment           $shipment
     * @param int                $oldPrice
     * @param bool               $isBusinessUnitDriver
     * @param ShipmentAssignment $oldAssignmentShipment
     */
    public function editInvoice(Shipment $shipment, $oldPrice, $isBusinessUnitDriver, ShipmentAssignment $oldAssignmentShipment = null)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $todayPeriodInvoice = $em->getRepository("AppBundle:PeriodInvoice")->getTodayPeriodInvoice();
        if ($todayPeriodInvoice instanceof PeriodInvoice) {
            if (!$isBusinessUnitDriver) {
                $previousPrice = intval($todayPeriodInvoice->getPrice());
                $newPrice = intval($shipment->getPrice());
                $totalPrice = ($previousPrice + $newPrice) - intval($oldPrice);
                $todayPeriodInvoice->setPrice($totalPrice);
                $shipmentItem = $todayPeriodInvoice->getShipments()->filter(function (Shipment $shipments) use ($shipment) {
                    if ($shipment == $shipments) {
                        return true;
                    }

                    return false;
                });
                if (!$shipmentItem->first() instanceof Shipment) {
                    $todayPeriodInvoice->addShipment($shipment);
                }
                if ($oldAssignmentShipment instanceof ShipmentAssignment) {
                    $oldAssignmentShipment->getDriver()->setStatus(Driver::STATUS_FREE);
                    $oldAssignmentShipment->setStatus(ShipmentAssignment::STATUS_CANCEL);
                    $em->persist($oldAssignmentShipment);
                    $em->flush($oldAssignmentShipment);
                }
                $em->persist($todayPeriodInvoice);
                $em->flush();
            } else {
                $shipmentItem = $todayPeriodInvoice->getShipments()->filter(function (Shipment $shipments) use ($shipment) {
                    if ($shipment == $shipments) {
                        return true;
                    }

                    return false;
                });
                if ($shipmentItem->first() instanceof Shipment) {
                    $previousPrice = intval($todayPeriodInvoice->getPrice());
                    $totalPrice = $previousPrice - intval($oldPrice);
                    $todayPeriodInvoice->setPrice($totalPrice);
                    $todayPeriodInvoice->removeShipment($shipmentItem[1]);

                    $em->persist($todayPeriodInvoice);
                    $em->flush();
                }
                if ($oldAssignmentShipment instanceof ShipmentAssignment) {
                    $oldAssignmentShipment->getDriver()->setStatus(Driver::STATUS_FREE);
                    $oldAssignmentShipment->setStatus(ShipmentAssignment::STATUS_CANCEL);
                    $em->persist($oldAssignmentShipment);
                    $em->flush($oldAssignmentShipment);
                }
            }
        } else {
            throw new Exception('Can not edit shipment');
        }
    }
}
