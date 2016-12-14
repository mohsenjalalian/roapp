<?php

namespace Roapp\RestaurantBundle\Entity;

use AppBundle\Entity\Shipment;
use Doctrine\ORM\Mapping as ORM;

/**
 * RestaurantShipment
 *
 * @ORM\Table(name="restaurant_shipment")
 * @ORM\Entity(repositoryClass="Roapp\RestaurantBundle\Repository\RestaurantShipmentRepository")
 */
class RestaurantShipment extends Shipment
{
    /**
     * @ORM\Column(name="test", type="string")
     */
    private $test;
}
