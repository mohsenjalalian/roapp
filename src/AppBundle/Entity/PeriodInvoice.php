<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * Class PeriodInvoice
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PeriodInvoiceRepository")
 */
class PeriodInvoice extends AbstractInvoice
{
    /**
     * @ManyToMany(targetEntity="AppBundle\Entity\Shipment")
     * @JoinTable(name="period_shipment",
     *      joinColumns={@JoinColumn(name="invoice_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="shipment_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $shipments;

    /**
     * PeriodInvoice constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->shipments = new ArrayCollection();
    }

    /**
     * Add shipment
     *
     * @param \AppBundle\Entity\Shipment $shipment
     *
     * @return PeriodInvoice
     */
    public function addShipment(Shipment $shipment)
    {
        $this->shipments[] = $shipment;

        return $this;
    }

    /**
     * Remove shipment
     *
     * @param \AppBundle\Entity\Shipment $shipment
     */
    public function removeShipment(Shipment $shipment)
    {
        $this->shipments->removeElement($shipment);
    }

    /**
     * Get shipments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShipments()
    {
        return $this->shipments;
    }
}
