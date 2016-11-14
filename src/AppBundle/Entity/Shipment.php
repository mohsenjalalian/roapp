<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Roapp\MediaBundle\Annotation\UploadableField;

/**
 * Shipment
 *
 * @ORM\Table(name="shipment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShipmentRepository")
 */
class Shipment
{
    const STATUS_ASSIGNED = 1;
    const STATUS_NOT_ASSIGNED = 0;
    const STATUS_ASSIGNMENT_SENT = 2;
    const STATUS_ASSIGNMENT_CANCEL = 3;
    
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Address" )
     */
    protected $otherAddress;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer" )
     */
    protected $other;

    /**
     * @ORM\Column(type="text", name="description")
     */
    protected $description;

    /**
     * @ORM\Column(type="decimal", name="value")
     */
    protected $value;

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
     * @ORM\Column(type="string", name="shipment_type")
     */
    protected $type;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Media")
     * @ORM\JoinTable(name="shipment_photos",
     *      joinColumns={@ORM\JoinColumn(name="shipment_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="photo_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $photos;

    /**
     * @var
     * @UploadableField(mappedAttribute="photos", mediaName="shipment_image")
     */
    protected $photoFiles;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ShipmentAssignment",mappedBy="shipment")
     */
    private $assignments;
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
     * Set value
     *
     * @param string $value
     *
     * @return Shipment
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
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
    public function setOwnerAddress(\AppBundle\Entity\Address $ownerAddress = null)
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
     * Set type
     *
     * @param string $type
     *
     * @return Shipment
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->photos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->photoFiles = [];
    }

    /**
     * Add photo
     *
     * @param \AppBundle\Entity\Media $photo
     *
     * @return Shipment
     */
    public function addPhoto(\AppBundle\Entity\Media $photo)
    {
        $this->photos[] = $photo;

        return $this;
    }

    /**
     * Remove photo
     *
     * @param \AppBundle\Entity\Media $photo
     */
    public function removePhoto(\AppBundle\Entity\Media $photo)
    {
        $this->photos->removeElement($photo);
    }

    /**
     * Get photos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Add assignment
     *
     * @param \AppBundle\Entity\ShipmentAssignment $assignment
     *
     * @return Shipment
     */
    public function addAssignment(\AppBundle\Entity\ShipmentAssignment $assignment)
    {
        $this->assignments[] = $assignment;

        return $this;
    }

    /**
     * Remove assignment
     *
     * @param \AppBundle\Entity\ShipmentAssignment $assignment
     */
    public function removeAssignment(\AppBundle\Entity\ShipmentAssignment $assignment)
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
    public function setOtherAddress(\AppBundle\Entity\Address $otherAddress = null)
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
     * Set other
     *
     * @param \AppBundle\Entity\Customer $other
     *
     * @return Shipment
     */
    public function setOther(\AppBundle\Entity\Customer $other = null)
    {
        $this->other = $other;

        return $this;
    }

    /**
     * Get other
     *
     * @return \AppBundle\Entity\Customer
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * Add photo
     *
     * @param \AppBundle\Entity\Media $photo
     *
     * @return Shipment
     */
    public function addPhotoFile($photoFile)
    {
        $this->photoFiles[] = $photoFile;

        return $this;
    }

    /**
     * Remove photo
     *
     * @param $photo
     */
    public function removePhotoFile($photoFile)
    {
        $index = array_search($photoFile, $this->photoFiles);

        if ($index != false) {
            unset($this->photoFiles[$index]);
        }
    }

    /**
     * Get photos
     *
     * @return array
     */
    public function getPhotoFiles()
    {
        return $this->photoFiles;
    }
}
