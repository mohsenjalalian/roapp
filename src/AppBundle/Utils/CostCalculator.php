<?php

namespace AppBundle\Utils;
use AppBundle\Entity\Address;
use GuzzleHttp\Client;

/**
 *class CostCalculator
 */
class CostCalculator
{
    private $timeMapping;
    private $valueMapping;
    /**
     * CostCalculator constructor.
     *
     * @param array $timeMapping
     * @param array $valueMapping
     */
    public function __construct($timeMapping, $valueMapping)
    {
        $this->timeMapping = $timeMapping;
        $this->valueMapping = $valueMapping;
    }

    /**
     * calculates the cost for shipment
     * 
     * @param Address   $source        Source address
     * @param Address   $destination   Destination address
     * @param int       $shipmentValue Value of shipment
     * @param \DateTime $orderDateTime Order date and time
     *
     * @return int
     */
    public function getCost(Address $source, Address $destination, $shipmentValue, \DateTime $orderDateTime) {

        return 1000;
    }

}