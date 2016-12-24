<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * ShipmentHistory
 *
 * @ORM\Table(name="shipment_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShipmentHistoryRepository")
 */
class ShipmentHistory
{
    const ACTION_CREATE = 0;
    const ACTION_PAY = 1;
    const ACTION_SEND_ASSIGNMENT = 2;
    const ACTION_REJECT = 3;
    const ACTION_ACCEPT_BY_DRIVER = 4;
    const ACTION_REJECT_BY_DRIVER = 5;
    const ACTION_START_PICKUP = 6;
    const ACTION_PICKUP = 7;
    const ACTION_START_DELIVERY = 8;
    const ACTION_DELIVER = 9;
    const ACTION_CANCEL_BY_CUSTOMER = 10;
    const ACTION_FAIL_BY_CUSTOMER = 11;
    const ACTION_FAIL_BY_DRIVER = 12;
    const ACTION_TIMEOUT = 13;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Person")
     */
    private $actor;

    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Shipment")
     */
    private $shipment;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer", name="action")
     */
    protected $action;

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="json_array", nullable=true)
     */
    private $data;

    /**
     * Constructor
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ShipmentHistory
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
     * Set action
     *
     * @param integer $action
     *
     * @return ShipmentHistory
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return integer
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set data
     *
     * @param array $data
     *
     * @return ShipmentHistory
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set actor
     *
     * @param \AppBundle\Entity\Person $actor
     *
     * @return ShipmentHistory
     */
    public function setActor(\AppBundle\Entity\Person $actor = null)
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * Get actor
     *
     * @return \AppBundle\Entity\Person
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * Set shipment
     *
     * @param \AppBundle\Entity\Shipment $shipment
     *
     * @return ShipmentHistory
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
}
