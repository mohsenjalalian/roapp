<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessUnit
 *
 * @ORM\Table(name="business_unit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BusinessUnitRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="business_type", type="string")
 * @ORM\DiscriminatorMap({"" = "BusinessUnit"})
 */
class BusinessUnit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Customer", mappedBy="businessUnit")
     */
    protected $customers;

    /**
     * @var integer
     *
     *  * @ORM\Column(name="contract_type", type="enumContract")
     */
    protected $contractType;

    /**
     * @ORM\ManyToOne(targetEntity="BusinessType", inversedBy="businessUnits")
     * @ORM\JoinColumn(name="business_type_id", referencedColumnName="id")
     */
    protected $businessType;

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
     * Constructor
     */
    public function __construct()
    {
        $this->customers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return BusinessUnit
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add customer
     *
     * @param \AppBundle\Entity\Customer $customer
     *
     * @return BusinessUnit
     */
    public function addCustomer(\AppBundle\Entity\Customer $customer)
    {
        $this->customers[] = $customer;

        return $this;
    }

    /**
     * Remove customer
     *
     * @param \AppBundle\Entity\Customer $customer
     */
    public function removeCustomer(\AppBundle\Entity\Customer $customer)
    {
        $this->customers->removeElement($customer);
    }

    /**
     * Get customers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * Set contractType
     *
     * @param \enumContract $contractType
     *
     * @return BusinessUnit
     */
    public function setContractType($contractType)
    {
        $this->contractType = $contractType;

        return $this;
    }

    /**
     * Get contractType
     *
     * @return \enumContract
     */
    public function getContractType()
    {
        return $this->contractType;
    }

    /**
     * Set businessType
     *
     * @param \AppBundle\Entity\BusinessType $businessType
     *
     * @return BusinessUnit
     */
    public function setBusinessType(\AppBundle\Entity\BusinessType $businessType = null)
    {
        $this->businessType = $businessType;

        return $this;
    }

    /**
     * Get businessType
     *
     * @return \AppBundle\Entity\BusinessType
     */
    public function getBusinessType()
    {
        return $this->businessType;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
