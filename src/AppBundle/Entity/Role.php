<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * Role
 *
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 */
class Role
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    private $label;
    
    /**
     * @var int
     * 
     * @ManyToMany(targetEntity="AppBundle\Entity\Operator", mappedBy="roleId")
     */
    
    private $operatorId;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->operatorId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Role
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
     * Set label
     *
     * @param string $label
     *
     * @return Role
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Add operatorId
     *
     * @param \AppBundle\Entity\Operator $operatorId
     *
     * @return Role
     */
    public function addOperatorId(\AppBundle\Entity\Operator $operatorId)
    {
        $this->operatorId[] = $operatorId;

        return $this;
    }

    /**
     * Remove operatorId
     *
     * @param \AppBundle\Entity\Operator $operatorId
     */
    public function removeOperatorId(\AppBundle\Entity\Operator $operatorId)
    {
        $this->operatorId->removeElement($operatorId);
    }

    /**
     * Get operatorId
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOperatorId()
    {
        return $this->operatorId;
    }
}
