<?php

namespace AppBundle\Utils\Shipment;

/**
 * Interface ShipmentInterface
 * @package AppBundle\Utils\Shipment
 */
interface ShipmentProcessInterface
{
    /**
     * @return mixed
     */
    public function create();

    /**
     * @return mixed
     */
    public function read();

    /**
     * @return mixed
     */
    public function readAll();

    /**
     * @return mixed
     */
    public function edit();
}
