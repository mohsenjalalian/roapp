<?php

namespace Roapp\RestaurantBundle\Entity;

use AppBundle\Entity\Address;
use AppBundle\Entity\BusinessUnit;
use Doctrine\ORM\Mapping as ORM;

/**
 * RestaurantShipment
 *
 * @ORM\Table(name="restaurant")
 * @ORM\Entity(repositoryClass="Roapp\RestaurantBundle\Repository\RestaurantRepository")
 */
class Restaurant extends BusinessUnit
{
    /**
     * @var Address
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Address")
     */
    private $defaultAddress;

    /**
     * Set defaultAddress
     *
     * @param \AppBundle\Entity\Address $defaultAddress
     *
     * @return Restaurant
     */
    public function setDefaultAddress(\AppBundle\Entity\Address $defaultAddress = null)
    {
        $this->defaultAddress = $defaultAddress;

        return $this;
    }

    /**
     * Get defaultAddress
     *
     * @return \AppBundle\Entity\Address
     */
    public function getDefaultAddress()
    {
        return $this->defaultAddress;
    }
}
