<?php
/**
 * User: mohsenjalalian
 * Date: 12/12/16
 * Time: 4:22 PM
 */
namespace Roapp\RestaurantBundle\Utils\Services;

use AppBundle\Utils\Shipment\ShipmentInterface;

/**
 * Class RestaurantShipmentService
 * @package RestaurantBundle\Utils\Services
 */
class RestaurantShipmentService implements ShipmentInterface
{
    /**
     * @var ShipmentInterface[]
     */
    private $shipments;
    /**
     * @param string            $shipmentServiceName
     * @param ShipmentInterface $shipment
     */
    public function addShipment($shipmentServiceName, ShipmentInterface $shipment)
    {
        $this->shipments[$shipmentServiceName] = $shipment;
    }
}
