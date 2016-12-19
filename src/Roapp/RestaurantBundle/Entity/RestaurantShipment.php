<?php

namespace Roapp\RestaurantBundle\Entity;

use AppBundle\Annotation\BusinessType;
use AppBundle\Entity\Shipment;
use Doctrine\ORM\Mapping as ORM;

/**
 * RestaurantShipment
 *
 * @ORM\Table(name="restaurant_shipment")
 * @ORM\Entity(repositoryClass="Roapp\RestaurantBundle\Repository\RestaurantShipmentRepository")
 * @BusinessType()
 */
class RestaurantShipment extends Shipment
{
}
