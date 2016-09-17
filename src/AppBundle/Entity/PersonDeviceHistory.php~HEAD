<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PersonDeviceHistory
 *
 * @ORM\Table(name="person_device_history")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonDeviceHistoryRepository")
 */
class PersonDeviceHistory
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
     * @var integer
     *
     * @ORM\Column(name="action", type="enumCustomerDeviceHistoryAction")
     */
    private $action;

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="json_array", nullable=true)
     */
    private $data;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time", type="datetime", nullable=true)
     */
    private $dateTime;

    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\PersonDevice")
     */
    private $personDevice;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", options={"default"=false})
     */
    private $status = false;

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
     * Set action
     *
     * @param string $action
     *
     * @return PersonDeviceHistory
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return array
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
     * @return PersonDeviceHistory
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
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return PersonDeviceHistory
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
     * Set personDevice
     *
     * @param PersonDevice $personDevice
     *
     * @return PersonDeviceHistory
     */
    public function setPersonDevice($personDevice)
    {
        $this->personDevice = $personDevice;

        return $this;
    }

    /**
     * Get personDevice
     *
     * @return PersonDevice
     */
    public function getPersonDevice()
    {
        return $this->personDevice;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return boolean
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }
}

