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
     * @ORM\Column(name="test", type="string")
     */
    private $test;

    /**
     * @ORM\Column(name="test2", type="string")
     */
    private $test2;

    /**
     * Set test
     *
     * @param string $test
     *
     * @return RestaurantShipment
     */
    public function setTest($test)
    {
        $this->test = $test;

        return $this;
    }

    /**
     * Get test
     *
     * @return string
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * Set test2
     *
     * @param string $test2
     *
     * @return RestaurantShipment
     */
    public function setTest2($test2)
    {
        $this->test2 = $test2;

        return $this;
    }

    /**
     * Get test2
     *
     * @return string
     */
    public function getTest2()
    {
        return $this->test2;
    }
}
