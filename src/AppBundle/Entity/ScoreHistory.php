<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScoreHistory
 *
 * @ORM\Entity
 * @ORM\Table(name="score_history")
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
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Person")
     */
    private $person;

    /**
     * @ORM\Column(name="reason", type="text")
     */
    private $reason;

    /**
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Person")
     */
    private $author;

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
     * @param integer $delta
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
     * @return integer
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ScoreHistory
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
     * Set person
     *
     * @param \AppBundle\Entity\Person $person
     *
     * @return ScoreHistory
     */
    public function setPerson(\AppBundle\Entity\Person $person = null)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \AppBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\Person $author
     *
     * @return ScoreHistory
     */
    public function setAuthor(\AppBundle\Entity\Person $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\Person
     */
    public function getAuthor()
    {
        return $this->author;
    }
}