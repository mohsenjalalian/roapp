<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScoreHistory
 *
 * @ORM\Entity
 * @ORM\Table(name="score_history")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="object_type", type="string")
 * @ORM\DiscriminatorMap({"score_history" = "ScoreHistory", "driver_score_history" = "DriverScoreHistory", "customer_score_history" = "CustomerScoreHistory"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ScoreHistoryRepository")
 */
class ScoreHistory
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
     * @ORM\Column(name="object_id", type="integer")
     */
    private $objectId;

    /**
     * @ORM\Column(name="reason", type="text")
     */
    private $reason;

    /**
     * @ORM\Column(name="delta", type="decimal", precision=8, scale=6)
     */
    private $delta;

    /**
     * @ORM\Column(name="date_time", type="datetime")
     */
    private $dateTime;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\Person")
     */
    private $author;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->author = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set objectId
     *
     * @param integer $objectId
     *
     * @return ScoreHistory
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return ScoreHistory
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
     * Set delta
     *
     * @param string $delta
     *
     * @return ScoreHistory
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * Get delta
     *
     * @return string
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return ScoreHistory
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
     * Add author
     *
     * @param \AppBundle\Entity\Person $author
     *
     * @return ScoreHistory
     */
    public function addAuthor(\AppBundle\Entity\Person $author)
    {
        $this->author[] = $author;

        return $this;
    }

    /**
     * Remove author
     *
     * @param \AppBundle\Entity\Person $author
     */
    public function removeAuthor(\AppBundle\Entity\Person $author)
    {
        $this->author->removeElement($author);
    }

    /**
     * Get author
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
