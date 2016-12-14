<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var string
     *
     * @ORM\Column(name="entity_namespace", type="string", unique=true)
     */
    private $entityNamespace;

    /**
     * @var string
     *
     * @ORM\Column(name="form_namespace", type="string", unique=true)
     */
    private $formNamespace;

    /**
     * @var string
     *
     * @ORM\Column(name="bundle_namespace", type="string", unique=true, options={"default"=""})
     */
    private $bundleNamespace;

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
        $this->businessUnits = new ArrayCollection();
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
    public function addBusinessUnit(BusinessUnit $businessUnit)
    {
        $this->businessUnits[] = $businessUnit;

        return $this;
    }

    /**
     * Remove businessUnit
     *
     * @param \AppBundle\Entity\BusinessUnit $businessUnit
     */
    public function removeBusinessUnit(BusinessUnit $businessUnit)
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

    /**
     * Display as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set entityNamespace
     *
     * @param string $entityNamespace
     *
     * @return BusinessType
     */
    public function setEntityNamespace($entityNamespace)
    {
        $this->entityNamespace = $entityNamespace;

        return $this;
    }

    /**
     * Get entityNamespace
     *
     * @return string
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }

    /**
     * Set bundleNamespace
     *
     * @param string $bundleNamespace
     *
     * @return BusinessType
     */
    public function setBundleNamespace($bundleNamespace)
    {
        $this->bundleNamespace = $bundleNamespace;

        return $this;
    }

    /**
     * Get bundleNamespace
     *
     * @return string
     */
    public function getBundleNamespace()
    {
        return $this->bundleNamespace;
    }

    /**
     * Set formNamespace
     *
     * @param string $formNamespace
     *
     * @return BusinessType
     */
    public function setFormNamespace($formNamespace)
    {
        $this->formNamespace = $formNamespace;

        return $this;
    }

    /**
     * Get formNamespace
     *
     * @return string
     */
    public function getFormNamespace()
    {
        return $this->formNamespace;
    }
}
