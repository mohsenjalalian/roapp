<?php

namespace AppBundle\Utils\Services;

use AppBundle\Entity\Customer;
use AppBundle\Entity\Shipment;
use AppBundle\Utils\Shipment\ShipmentProcessInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\Invoice;
use AppBundle\Entity\Address;
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
     * @param Request  $request
     * @param Form     $form
     *
     */
    public function create(Shipment $shipment, Request $request, $form)
    {
        $invoice = new Invoice();
        $ownerAddressId =  $request->request->get('publicAddress');
        $otherPhoneNumber = $form->get('other')->getData();
        $otherAddressId = $request->request->get('reciver_public_address');
        $ownerAddress = $this->entityManager
            ->getRepository("AppBundle:Address")
            ->find($ownerAddressId)
        ;
        /** @var Customer $customer */
        $customer = $this->entityManager
            ->getRepository('AppBundle:Customer')
            ->findOrCreateByPhone($otherPhoneNumber);

        $shipment->setOther($customer);
        $shipment->setOwnerAddress($ownerAddress);

        if ($otherAddressId) {
            $otherAddress = $this->entityManager
                ->getRepository("AppBundle:Address")
                ->find($otherAddressId)
            ;
            $shipment->setOtherAddress($otherAddress);
        }

        $shipmentPrice = $request
            ->request->get('price_shipment');

        $createdAt = new \DateTime();
        $shipment->setPrice(floatval($shipmentPrice));
        $shipment->setCreatedAt($createdAt);
        $shipment->setStatus(Shipment::STATUS_WAITING_FOR_PAYMENT);
        $shipment->setType("send");
        $invoice->setCreatedAt($createdAt);
        $invoice->setStatus(Invoice::STATUS_UNPAID);
        $invoice->setPrice(floatval($shipmentPrice));
        $shipment->setInvoice($invoice);

        $em = $this->entityManager;
        $em->persist($shipment);
        $em->persist($invoice);
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
}
