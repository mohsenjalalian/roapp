<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * CustomerDevice
 *
 * @ORM\Table(name="customer_device")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CustomerDeviceRepository")
 */
class CustomerDevice
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
     * @ORM\Column(name="installed_at", type="datetime", nullable=true)
     */
    private $installedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_access_at", type="datetime", nullable=true)
     */
    private $lastAccessAt;

    /**
     * @var string
     *
     * @ORM\Column(name="device_uuid", type="string", length=255)
     * @Assert\NotBlank(message="Device UUID must not be blank")
     */
    private $deviceUuid;

    /**
     * @var string
     *
     * @ORM\Column(name="device_token", type="string", length=255, nullable=true, unique=true)
     */
    private $deviceToken;

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
     * @var int
     *
     * @ORM\Column(name="device_type", type="integer")
     * @Assert\NotBlank(message="Device type must not be blank")
     */
    private $deviceType;

    /**
     * @var bool
     *
     * @ORM\Column(name="phone_verification_status", type="boolean", options={"default"=false})
     */
    private $phoneVerificationStatus = false;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_verification_code", type="string", length=255, nullable=true)
     */
    private $phoneVerificationCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="phone_verification_sent", type="datetime", nullable=true, nullable=true)
     */
    private $phoneVerificationSent;

    /**
     * @var string
     *
     * @ORM\Column(name="notification_token", type="string", length=255, nullable=true)
     */
    private $notificationToken;

    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Customer")
     */
    private $customer;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set installedAt
     *
     * @param \DateTime $installedAt
     *
     * @return CustomerDevice
     */
    public function setInstalledAt($installedAt)
    {
        $this->installedAt = $installedAt;

        return $this;
    }

    /**
     * Get installedAt
     *
     * @return \DateTime
     */
    public function getInstalledAt()
    {
        return $this->installedAt;
    }

    /**
     * Set lastAccessAt
     *
     * @param \DateTime $lastAccessAt
     *
     * @return CustomerDevice
     */
    public function setLastAccessAt($lastAccessAt)
    {
        $this->lastAccessAt = $lastAccessAt;

        return $this;
    }

    /**
     * Get lastAccessAt
     *
     * @return \DateTime
     */
    public function getLastAccessAt()
    {
        return $this->lastAccessAt;
    }

    /**
     * Set deviceUuid
     *
     * @param string $deviceUuid
     *
     * @return CustomerDevice
     */
    public function setDeviceUuid($deviceUuid)
    {
        $this->deviceUuid = $deviceUuid;

        return $this;
    }

    /**
     * Get deviceUuid
     *
     * @return string
     */
    public function getDeviceUuid()
    {
        return $this->deviceUuid;
    }

    /**
     * Set deviceToken
     *
     * @param string $deviceToken
     *
     * @return CustomerDevice
     */
    public function setDeviceToken($deviceToken)
    {
        $this->deviceToken = $deviceToken;

        return $this;
    }

    /**
     * Get deviceToken
     *
     * @return string
     */
    public function getDeviceToken()
    {
        return $this->deviceToken;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return CustomerDevice
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
     * @return CustomerDevice
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
     * Set deviceType
     *
     * @param integer $deviceType
     *
     * @return CustomerDevice
     */
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;

        return $this;
    }

    /**
     * Get deviceType
     *
     * @return integer
     */
    public function getDeviceType()
    {
        return $this->deviceType;
    }

    /**
     * Set phoneVerificationStatus
     *
     * @param boolean $phoneVerificationStatus
     *
     * @return CustomerDevice
     */
    public function setPhoneVerificationStatus($phoneVerificationStatus)
    {
        $this->phoneVerificationStatus = $phoneVerificationStatus;

        return $this;
    }

    /**
     * Get phoneVerificationStatus
     *
     * @return boolean
     */
    public function getPhoneVerificationStatus()
    {
        return $this->phoneVerificationStatus;
    }

    /**
     * Set phoneVerificationCode
     *
     * @param string $phoneVerificationCode
     *
     * @return CustomerDevice
     */
    public function setPhoneVerificationCode($phoneVerificationCode)
    {
        $this->phoneVerificationCode = $phoneVerificationCode;

        return $this;
    }

    /**
     * Get phoneVerificationCode
     *
     * @return string
     */
    public function getPhoneVerificationCode()
    {
        return $this->phoneVerificationCode;
    }

    /**
     * Set phoneVerificationSent
     *
     * @param \DateTime $phoneVerificationSent
     *
     * @return CustomerDevice
     */
    public function setPhoneVerificationSent($phoneVerificationSent)
    {
        $this->phoneVerificationSent = $phoneVerificationSent;

        return $this;
    }

    /**
     * Get phoneVerificationSent
     *
     * @return \DateTime
     */
    public function getPhoneVerificationSent()
    {
        return $this->phoneVerificationSent;
    }

    /**
     * Set notificationToken
     *
     * @param string $notificationToken
     *
     * @return CustomerDevice
     */
    public function setNotificationToken($notificationToken)
    {
        $this->notificationToken = $notificationToken;

        return $this;
    }

    /**
     * Get notificationToken
     *
     * @return string
     */
    public function getNotificationToken()
    {
        return $this->notificationToken;
    }

    /**
     * Set customer
     *
     * @param \AppBundle\Entity\Customer $customer
     *
     * @return CustomerDevice
     */
    public function setCustomer(\AppBundle\Entity\Customer $customer = null)
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
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     * @param $payload
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        // check if the name is actually a fake name
        if (empty($this->getLatitude()) xor empty($this->getLongitude())) {
            if (empty($this->getLatitude())) {
                $context->buildViolation('This value should not be blank')
                    ->atPath('latitude')
                    ->addViolation();
            }

            if (empty($this->getLongitude())) {
                $context->buildViolation('This value should not be blank')
                    ->atPath('longitude')
                    ->addViolation();
            }
        }
    }
}
