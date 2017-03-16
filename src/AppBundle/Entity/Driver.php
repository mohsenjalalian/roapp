<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation\Permissions;
use AppBundle\Annotation\Permission as PermissionAnnotation;

/**
 * Driver
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DriverRepository")
 * @Permissions(permissions={
 *     @PermissionAnnotation(mappedConst=Address::PERMISSION_ADD, type="class", label="افزودن آدرس",
 *          scopes={"AppBundle\Entity\Customer", "AppBundle\Entity\Operator", "AppBundle\Entity\Driver"}
 *     ),
 *     @PermissionAnnotation(mappedConst=Address::PERMISSION_EDIT, type="object", label="ویرایش آدرس",
 *          scopes={"AppBundle\Entity\Customer"}
 *     )
 * })
 */
class Driver extends Person
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
     * @var string
     */
    private $currentPassword;
    /**
     * @var string
     */
    private $newPassword;

    /**
     * @ManyToOne(targetEntity="BusinessUnit")
     * @JoinColumn(name="business_unit_id", referencedColumnName="id")
     */
    private $businessUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", name="verify_email_token", nullable=true)
     */
    private $verifyEmailToken;

    /**
     * @ORM\Column(type="string", name="image_name", nullable=true)
     */
    private $imageName;

    /**
     * @inheritdoc
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_DRIVER'];
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
        parent::__construct();
        $this->status = self::STATUS_NOT_AVAILABLE;
        $this->isActive = true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getFullName().' ('.$this->getUsername().')';
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

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->password,
            // see section on salt below
            // $this->salt,
        ]);
        // TODO: Implement serialize() method.
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
        // TODO: Implement unserialize() method.
    }

    /**
     * @return string
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * @param string $currentPassword
     * @return $this
     */
    public function setCurrentPassword($currentPassword)
    {
        $this->currentPassword = $currentPassword;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     * @return $this
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBusinessUnit()
    {
        return $this->businessUnit;
    }

    /**
     * @param mixed $businessUnit
     * @return $this
     */
    public function setBusinessUnit($businessUnit)
    {
        $this->businessUnit = $businessUnit;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Driver
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
     * Set verifyEmailToken
     *
     * @param string $verifyEmailToken
     *
     * @return Driver
     */
    public function setVerifyEmailToken($verifyEmailToken)
    {
        $this->verifyEmailToken = $verifyEmailToken;

        return $this;
    }

    /**
     * Get verifyEmailToken
     *
     * @return string
     */
    public function getVerifyEmailToken()
    {
        return $this->verifyEmailToken;
    }

    /**
     * Set imageName
     *
     * @param string $imageName
     *
     * @return Driver
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }
}
