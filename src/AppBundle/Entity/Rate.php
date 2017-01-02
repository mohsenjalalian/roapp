<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rate
 *
 * @ORM\Table(name="rate")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RateRepository")
 */
class Rate
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
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Customer")
     */
    private $creator;


    /**
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\Shipment")
     */
    private $shipment;


    /**
     * @Assert\NotNull()
     * @ORM\Column(name="point", type="integer")
     */
    private $point;

    /**
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * Rate constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
     * Set point
     *
     * @param integer $point
     *
     * @return Rate
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return integer
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Rate
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set creator
     *
     * @param \AppBundle\Entity\Customer $creator
     *
     * @return Rate
     */
    public function setCreator(\AppBundle\Entity\Customer $creator = null)
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
     * Set shipment
     *
     * @param \AppBundle\Entity\Shipment $shipment
     *
     * @return Rate
     */
    public function setShipment(\AppBundle\Entity\Shipment $shipment = null)
    {
        $this->shipment = $shipment;

        return $this;
    }

    /**
     * Get shipment
     *
     * @return \AppBundle\Entity\Shipment
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Rate
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
}
