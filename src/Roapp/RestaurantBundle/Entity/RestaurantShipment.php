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
    /**
     * @ORM\Column(type="decimal", name="value")
     */
    private $value;

    /**
     * Set value
     *
     * @param string $value
     *
     * @return RestaurantShipment
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
