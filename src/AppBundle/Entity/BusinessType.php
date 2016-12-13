<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessType
 *
 * @ORM\Table(name="business_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BusinessTypeRepository")
 */
class BusinessType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="BusinessUnit", mappedBy="businessType")
     */
    private $businessUnits;


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
        $this->businessUnits = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return BusinessType
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
     * Add businessUnit
     *
     * @param \AppBundle\Entity\BusinessUnit $businessUnit
     *
     * @return BusinessType
     */
    public function addBusinessUnit(\AppBundle\Entity\BusinessUnit $businessUnit)
    {
        $this->businessUnits[] = $businessUnit;

        return $this;
    }

    /**
     * Remove businessUnit
     *
     * @param \AppBundle\Entity\BusinessUnit $businessUnit
     */
    public function removeBusinessUnit(\AppBundle\Entity\BusinessUnit $businessUnit)
    {
        $this->businessUnits->removeElement($businessUnit);
    }

    /**
     * Get businessUnits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessUnits()
    {
        return $this->businessUnits;
    }
}
