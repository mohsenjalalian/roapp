<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * Operator
 *
 * @ORM\Table(name="operator")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OperatorRepository")
 */
class Operator
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
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var int
     * 
     * @ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="operatorId")
     */
    private $roleId;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roleId = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set email
     *
     * @param string $email
     *
     * @return Operator
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Operator
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Add roleId
     *
     * @param \AppBundle\Entity\Role $roleId
     *
     * @return Operator
     */
    public function addRoleId(\AppBundle\Entity\Role $roleId)
    {
        $this->roleId[] = $roleId;

        return $this;
    }

    /**
     * Remove roleId
     *
     * @param \AppBundle\Entity\Role $roleId
     */
    public function removeRoleId(\AppBundle\Entity\Role $roleId)
    {
        $this->roleId->removeElement($roleId);
    }

    /**
     * Get roleId
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoleId()
    {
        return $this->roleId;
    }
}
