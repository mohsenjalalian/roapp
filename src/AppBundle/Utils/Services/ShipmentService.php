<?php

namespace AppBundle\Utils\Services;

use AppBundle\Entity\Shipment;
use AppBundle\Utils\Shipment\ShipmentProcessInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Address;

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
     * PaymentService constructor.
     * @param EntityManager $entityManager
     * @param Router        $router
     * @param TokenStorage  $tokenStorage
     */
    public function __construct(EntityManager $entityManager, Router $router, TokenStorage $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @param Shipment $shipment
     */
    public function create(Shipment $shipment)
    {
        $invoice = new Invoice();
        $addressEntity = new Address();
        $user = $this->tokenStorage->getToken()->getUser();
        $now = new \DateTime();
        $tomorrow = $now->add(new \DateInterval('P1D'));
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
        $nameSpace = $this->shipments['app.shipment.restaurant']->getNameSpace();
        $shipment = new $nameSpace();

        return $shipment;
    }
}
