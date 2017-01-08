<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Shipment
 *
 * @ORM\Table(name="shipment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShipmentRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="business_type", type="string")
 * @ORM\DiscriminatorMap({"shipment" = "Shipment"})
 */
class Shipment
{
    const STATUS_ASSIGNED = 1;
    const STATUS_NOT_ASSIGNED = 0;
    const STATUS_ASSIGNMENT_SENT = 2;
    const STATUS_CANCEL = 3;
    const STATUS_REJECT = 4;
    const STATUS_CUSTOMER_FAILED = 5;
    const STATUS_ON_PICK_UP = 6;
    const STATUS_PICKED_UP = 7;
    const STATUS_ON_DELIVERY = 8;
    const STATUS_DELIVERED = 9;
    const STATUS_FINISH = 10;
    const STATUS_WAITING_FOR_PAYMENT = 11;
    const STATUS_DRIVER_FAILED = 12;

    const TRACK_ENABLED_STATUSES = [
        self::STATUS_ON_PICK_UP,
    ];

    const TRACK_DISABLED_STATUSES = [
        self::STATUS_CUSTOMER_FAILED,
        self::STATUS_DRIVER_FAILED,
        self::STATUS_FINISH,
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Address" )
     */
    protected $ownerAddress;

    /**
     * @var string
     */
    protected $otherPhone;

    /**
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Address")
     */
    protected $otherAddress;

    /**
     * @ORM\Column(type="text", name="description")
     */
    protected $description;

    /**
     * @ORM\Column(type="integer", name="status")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", name="pick_up_time")
     */
    protected $pickUpTime;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="decimal", name="price",nullable=true)
     */
    protected $price;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ShipmentAssignment",mappedBy="shipment")
     */
    private $assignments;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_business_unit_driver", type="boolean", options={"default"=false})
     */
    private $isBusinessUnitDriver = false;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ShipmentHistory", mappedBy="shipment")
     */
    private $shipmentHistories;

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
     * Set description
     *
     * @param string $description
     *
     * @return Shipment
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
     * Set status
     *
     * @param integer $status
     *
     * @return Shipment
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
     * Set pickUpTime
     *
     * @param \DateTime $pickUpTime
     *
     * @return Shipment
     */
    public function setPickUpTime($pickUpTime)
    {
        $this->pickUpTime = $pickUpTime;

        return $this;
    }

    /**
     * Get pickUpTime
     *
     * @return \DateTime
     */
    public function getPickUpTime()
    {
        return $this->pickUpTime;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Shipment
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
     * Set price
     *
     * @param string $price
     *
     * @return Shipment
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set ownerAddress
     *
     * @param \AppBundle\Entity\Address $ownerAddress
     *
     * @return Shipment
     */
    public function setOwnerAddress(Address $ownerAddress = null)
    {
        $this->ownerAddress = $ownerAddress;

        return $this;
    }

    /**
     * Get ownerAddress
     *
     * @return \AppBundle\Entity\Address
     */
    public function getOwnerAddress()
    {
        return $this->ownerAddress;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->assignments = new ArrayCollection();
    }

    /**
     * Add assignment
     *
     * @param \AppBundle\Entity\ShipmentAssignment $assignment
     *
     * @return Shipment
     */
    public function addAssignment(ShipmentAssignment $assignment)
    {
        $this->assignments[] = $assignment;

        return $this;
    }

    /**
     * Remove assignment
     *
     * @param \AppBundle\Entity\ShipmentAssignment $assignment
     */
    public function removeAssignment(ShipmentAssignment $assignment)
    {
        $this->assignments->removeElement($assignment);
    }

    /**
     * Get assignments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssignments()
    {
        return $this->assignments;
    }

    /**
     * Set otherAddress
     *
     * @param \AppBundle\Entity\Address $otherAddress
     *
     * @return Shipment
     */
    public function setOtherAddress(Address $otherAddress = null)
    {
        $this->otherAddress = $otherAddress;

        return $this;
    }

    /**
     * Get otherAddress
     *
     * @return \AppBundle\Entity\Address
     */
    public function getOtherAddress()
    {
        return $this->otherAddress;
    }

    /**
     * @return string
     */
    public function getOtherPhone()
    {
        return $this->otherPhone;
    }

    /**
     * @param string $otherPhone
     */
    public function setOtherPhone($otherPhone)
    {
        $this->otherPhone = $otherPhone;
    }

    /**
     * Set isBusinessUnitDriver
     *
     * @param boolean $isBusinessUnitDriver
     *
     * @return Shipment
     */
    public function setIsBusinessUnitDriver($isBusinessUnitDriver)
    {
        $this->isBusinessUnitDriver = $isBusinessUnitDriver;

        return $this;
    }

    /**
     * Get isBusinessUnitDriver
     *
     * @return boolean
     */
    public function getIsBusinessUnitDriver()
    {
        return $this->isBusinessUnitDriver;
    }

    /**
     * Add shipmentHistory
     *
     * @param \AppBundle\Entity\ShipmentHistory $shipmentHistory
     *
     * @return Shipment
     */
    public function addShipmentHistory(\AppBundle\Entity\ShipmentHistory $shipmentHistory)
    {
        $this->shipmentHistories[] = $shipmentHistory;

        return $this;
    }

    /**
     * Remove shipmentHistory
     *
     * @param \AppBundle\Entity\ShipmentHistory $shipmentHistory
     */
    public function removeShipmentHistory(ShipmentHistory $shipmentHistory)
    {
        $this->shipmentHistories->removeElement($shipmentHistory);
    }

    /**
     * Get shipmentHistories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShipmentHistories()
    {
        return $this->shipmentHistories;
    }

    /**
     * @return Driver|null
     */
    public function getAssignedDriver()
    {
        $assigned = $this->assignments->filter(
            function (ShipmentAssignment $assignment) {
                if ($assignment->getStatus() == ShipmentAssignment::STATUS_ACCEPTED
                    && $assignment->getDriver() instanceof Driver
                ) {
                    return true;
                }

                return false;
            }
        );

        if ($assigned->first() instanceof ShipmentAssignment) {
            return $assigned->first()->getDriver();
        } else {
            return null;
        }
    }
}
