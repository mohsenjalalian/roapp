<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Driver
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DriverRepository")
 */
class Driver extends Person
{
    const STATUS_FREE = 1;
    const STATUS_BUSY = 0;
    const STATUS_IN_PROGRESS = 2;

    /**
     * @ORM\Column(name="password", type="string")
     */
    private $password;
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return array('ROLE_DRIVER');
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
        // TODO: Implement getPassword() method.
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->phone;
        // TODO: Implement getUsername() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    /**
     * Set username
     *
     * @param string $username
     *
     * @return Driver
     */
    public function setUsername($username)
    {
        $this->phone = $username;
        return $this;
    }
    /**
     * Set password
     *
     * @param string $password
     *
     * @return Driver
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Driver
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }
    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->status = 1;
        $this->isActive = true;
        $this->shipments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add shipment
     *
     * @param \AppBundle\Entity\Shipment $shipment
     *
     * @return Driver
     */
    public function addShipment(\AppBundle\Entity\Shipment $shipment)
    {
        $this->shipments[] = $shipment;

        return $this;
    }

    /**
     * Remove shipment
     *
     * @param \AppBundle\Entity\Shipment $shipment
     */
    public function removeShipment(\AppBundle\Entity\Shipment $shipment)
    {
        $this->shipments->removeElement($shipment);
    }

    /**
     * Get shipments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShipments()
    {
        return $this->shipments;
    }
    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Driver
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
}
