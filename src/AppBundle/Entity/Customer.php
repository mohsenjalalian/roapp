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
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    private $password;
    
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
    public function getPassword()
    {
        return $this->password;
    }


    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
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
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list (
//            $this->id,
//            $this->phone,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
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
     * Set password
     *
     * @param string $password
     *
     * @return Customer
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}
