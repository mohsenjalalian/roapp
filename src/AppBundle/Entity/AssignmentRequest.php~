<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * AssignmentRequest
 *
 * @ORM\Table(name="assignment_request")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssignmentRequestRepository")
 */
class AssignmentRequest
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateTime", type="datetime")
     */
    private $dateTime;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=255,nullable=true)
     */
    private $reason;
    
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Shipment",inversedBy="assignments")
     */
    private $shipment;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Driver")
     */
    private $driver;

    public function  __construct()
    {
        $this->dateTime= new \DateTime();
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
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return AssignmentRequest
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return AssignmentRequest
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return AssignmentRequest
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set shipment
     *
     * @param \AppBundle\Entity\Shipment $shipment
     *
     * @return AssignmentRequest
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
     * Set driver
     *
     * @param \AppBundle\Entity\Driver $driver
     *
     * @return AssignmentRequest
     */
    public function setDriver(\AppBundle\Entity\Driver $driver = null)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get driver
     *
     * @return \AppBundle\Entity\Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }
}
