<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Customer
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 */
class Customer extends Person implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(name="email", type="string", length=255, unique=true, nullable=true)
     *
     */
    private $email;
    
    /**
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    /**
     * Customer constructor
     */

    public function __construct()
    {
        $this->isActive = true;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return array('ROLE_CUSTOMER');
    }
    
    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->email;
    }


    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
        // TODO: Implement serialize() method.
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
//            $this->phone,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
        // TODO: Implement unserialize() method.
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setUsername($email)
    {
        $this->email = $email;

        return $this;
    }
    
    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Customer
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

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
