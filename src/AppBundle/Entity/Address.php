<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Security\AccessControlInterface;
use AppBundle\Annotation\Permissions;
use AppBundle\Annotation\Permission as PermissionAnnotation;

/**
 * Address
 *
 * @ORM\Table(name="address")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AddressRepository")
 * @Permissions(permissions={
 *     @PermissionAnnotation(mappedConst=Address::PERMISSION_ADD, type="class", label="افزودن آدرس",
 *          scopes={"AppBundle\Entity\Customer", "AppBundle\Entity\Operator"}
 *     ),
 *     @PermissionAnnotation(mappedConst=Address::PERMISSION_EDIT, type="object", label="ویرایش آدرس",
 *          scopes={"AppBundle\Entity\Customer"}
 *     )
 * })
 */
class Address implements AccessControlInterface
{
    /**
     * Permissions
     */
    const PERMISSION_ADD = 'add';
    const PERMISSION_EDIT = 'edit';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $customer;

    /**
     * @ORM\Column(name="is_public", type="boolean", nullable=true)
     */
    private $isPublic = false;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     */
    private $creator;

    /**
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="decimal", precision=10, scale=8, nullable=true)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="decimal", precision=11, scale=8, nullable=true)
     */
    private $longitude;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isPublic
     *
     * @param boolean $isPublic
     *
     * @return Address
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * Get isPublic
     *
     * @return boolean
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Address
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Address
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Address
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set customer
     *
     * @param \AppBundle\Entity\Customer $customer
     *
     * @return Address
     */
    public function setCustomer(Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \AppBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set creator
     *
     * @param \AppBundle\Entity\Customer $creator
     *
     * @return Address
     */
    public function setCreator(Customer $creator = null)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get creator
     *
     * @return \AppBundle\Entity\Customer
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getDescription();
    }
}
