<?php

namespace AppBundle\Utils\Shipment;

/**
 * Interface ShipmentInterface
 * @package AppBundle\Utils\Shipment
 */
interface ShipmentProcessInterface
{
    /**
     *
     */
    public function create();

    /**
     *
     */
    public function read();

    /**
     *
     */
    public function readAll();

    /**
     *
     */
    public function edit();

    /**
     * @return string
     */
    public function getShipmentEntityNameSpace();

    /**
     * @return string
     */
    public function getShipmentFormNameSpace();
}
