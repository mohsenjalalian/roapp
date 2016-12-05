<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Customer
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerRepository")
 */
class Customer extends Person implements AdvancedUserInterface, \Serializable
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
     * @inheritdoc
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_CUSTOMER');
    }

    /**
     * @inheritdoc
     * @return null
     */
    public function getSalt()
    {
        return null;
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritdoc
     * @return string
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
     * @return array
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt,
            $this->isActive,
        ));
        // TODO: Implement serialize() method.
    }

    /**
     * @inheritdoc
     * @param array $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
//            $this->phone,
            $this->password,
            // see section on salt below
            // $this->salt
            $this->isActive,
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

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->isActive;
    }
}
