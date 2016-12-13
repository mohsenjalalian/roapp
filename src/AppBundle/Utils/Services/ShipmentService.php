<?php

namespace AppBundle\Utils\Services;

use AppBundle\Utils\Shipment\ShipmentProcessInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

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
     * PaymentService constructor.
     * @param EntityManager $entityManager
     * @param Router        $router
     */
    public function __construct(EntityManager $entityManager, Router $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }
    /**
     *
     */
    public function create()
    {
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
     *
     */
    public function shipmentFactory()
    {
        $nameSpace = $this->shipments['app.shipment.restaurant']->getNameSpace();
        $shipment = new $nameSpace();
        dump($shipment);
    }
}
