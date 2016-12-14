<?php

namespace Roapp\RestaurantBundle\Entity;

use AppBundle\Annotation\BusinessTypeShipment;
use AppBundle\Entity\Shipment;
use Doctrine\ORM\Mapping as ORM;

/**
 * RestaurantShipment
 *
 * @ORM\Table(name="restaurant_shipment")
 * @ORM\Entity(repositoryClass="Roapp\RestaurantBundle\Repository\RestaurantShipmentRepository")
 * @BusinessTypeShipment()
 */
class RestaurantShipment extends Shipment
{
}
