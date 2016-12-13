<?php

namespace AppBundle\Utils\Services;

use AppBundle\Utils\Shipment\ShipmentInterface;
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
     * @var ShipmentInterface[]
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
     * @param string            $shipmentServiceName
     * @param ShipmentInterface $shipment
     */
    public function addShipment($shipmentServiceName, ShipmentInterface $shipment)
    {
        $this->shipments[$shipmentServiceName] = $shipment;
    }
}
