<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Driver
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DriverRepository")
 */
class Driver extends Person implements UserInterface
{
    const STATUS_FREE = 1;
    const STATUS_BUSY = 0;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_NOT_AVAILABLE = 3;

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
     * Constructor
     */
    public function __construct()
    {
        $this->status = self::STATUS_FREE;
        $this->isActive = true;
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
