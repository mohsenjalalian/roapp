<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * Class ShipmentInvoice
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShipmentInvoiceRepository")
 */
class ShipmentInvoice extends AbstractInvoice
{
    /**
     * @OneToOne(targetEntity="AppBundle\Entity\Shipment")
     * @JoinColumn(name="shipment_invoice_id", referencedColumnName="id")
     */
    private $shipment;


    /**
     * Set shipment
     *
     * @param \AppBundle\Entity\Shipment $shipment
     *
     * @return ShipmentInvoice
     */
    public function setShipment(Shipment $shipment = null)
    {
        $this->shipment = $shipment;

        return $this;
    }

    /**
     * Get shipment
     *
     * @return \AppBundle\Entity\Shipment
     */
    public function getShipment()
    {
        return $this->shipment;
    }
}
