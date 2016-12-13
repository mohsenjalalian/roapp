<?php
/**
 * User: mohsenjalalian
 * Date: 12/12/16
 * Time: 4:22 PM
 */
namespace Roapp\RestaurantBundle\Utils\Services;

use AppBundle\Utils\Shipment\ShipmentProcessInterface;
use Roapp\RestaurantBundle\Entity\RestaurantShipment;

/**
 * Class RestaurantShipmentService
 * @package RestaurantBundle\Utils\Services
 */
class RestaurantShipmentService implements ShipmentProcessInterface
{
    /**
     * @return mixed
     */
    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     * @return mixed
     */
    public function read()
    {
        // TODO: Implement read() method.
    }

    /**
     * @return mixed
     */
    public function readAll()
    {
        // TODO: Implement readAll() method.
    }

    /**
     * @return mixed
     */
    public function edit()
    {
        // TODO: Implement edit() method.
    }

    /**
     * @return mixed
     */
    public function getNameSpace()
    {
        return RestaurantShipment::class;
    }
}
