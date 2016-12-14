<?php
/**
 * User: mohsenjalalian
 * Date: 12/12/16
 * Time: 4:22 PM
 */
namespace Roapp\RestaurantBundle\Utils\Services;

use AppBundle\Utils\Shipment\ShipmentProcessInterface;
use Roapp\RestaurantBundle\Entity\RestaurantShipment;
use Roapp\RestaurantBundle\Form\RestaurantShipmentType;
use Roapp\RestaurantBundle\RoappRestaurantBundle;

/**
 * Class RestaurantShipmentService
 * @package RestaurantBundle\Utils\Services
 */
class RestaurantShipmentService implements ShipmentProcessInterface
{
    /**
     * Create process
     */
    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     *
     */
    public function read()
    {
        // TODO: Implement read() method.
    }

    /**
     *
     */
    public function readAll()
    {
        // TODO: Implement readAll() method.
    }

    /**
     *
     */
    public function edit()
    {
        // TODO: Implement edit() method.
    }

    /**
     * @return string
     */
    public function getShipmentEntityNameSpace()
    {
        return RoappRestaurantBundle::getShipmentEntityNamespace();
    }

    /**
     * @return string
     */
    public function getShipmentFormNamespace()
    {
        return RoappRestaurantBundle::getShipmentFormNamespace();
    }
}
