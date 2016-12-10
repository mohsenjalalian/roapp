<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping\ManyToMany;

// @codingStandardsIgnoreStart
/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 * @ORM\Table(name="person")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"customer" = "Customer", "driver" = "Driver" , "operator" = "Operator"})
 * @UniqueEntity(fields="phone", message="شماره تلفن وارد شده تکراری است.")
 * @UniqueEntity(fields="email", message="پست الکترونیکی وارد شده تکراری است.")
 */
abstract class Person implements UserInterface, \Serializable
{
    // @codingStandardsIgnoreEnd
    //TODO: Refactor className to AbstractPerson
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, unique=true)
     */
    protected $phone;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive = false;

    /**
     * @ORM\Column(name="full_name", type="string" , nullable=true)
     */
    protected $fullName;

    /**
     * @ORM\Column(name="password", type="string",  nullable=true)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="score", type="decimal", precision=8, scale=6, nullable=true)
     */
    protected $score;

    /**
     * @var string
     * @ORM\Column(name="remember_token", type="string", nullable=true)
     */
    protected $rememberToken;

    /**
     * @var string
     * @ORM\Column(name="activation_token", type="string", nullable=true)
     */
    protected $activationToken;

    /**
     * @OneToMany(targetEntity="AppBundle\Entity\Payment", mappedBy="person")
     */
    protected $payment;

    /**
     * @var int
     *
     * @ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="people")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="scope", type="string", length=255)
     */
    private $scope;

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
     * Set phone
     *
     * @param string $phone
     *
     * @return UserInterface
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return Person
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Person
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
     * Set score
     *
     * @param string $score
     *
     * @return Person
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return string
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set rememberToken
     *
     * @param string $rememberToken
     *
     * @return Person
     */
    public function setRememberToken($rememberToken)
    {
        $this->rememberToken = $rememberToken;

        return $this;
    }

    /**
     * Get rememberToken
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    /**
     * Set activationToken
     *
     * @param string $activationToken
     *
     * @return Person
     */
    public function setActivationToken($activationToken)
    {
        $this->activationToken = $activationToken;

        return $this;
    }

    /**
     * Get activationToken
     *
     * @return string
     */
    public function getActivationToken()
    {
        return $this->activationToken;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->payment = new ArrayCollection();
    }

    /**
     * Add payment
     *
     * @param \AppBundle\Entity\Payment $payment
     *
     * @return Person
     */
    public function addPayment(Payment $payment)
    {
        $this->payment[] = $payment;

        return $this;
    }

    /**
     * Remove payment
     *
     * @param \AppBundle\Entity\Payment $payment
     */
    public function removePayment(Payment $payment)
    {
        $this->payment->removeElement($payment);
    }

    /**
     * Get payment
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Add role
     *
     * @param \AppBundle\Entity\Role $role
     *
     * @return Person
     */
    public function addRole(\AppBundle\Entity\Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \AppBundle\Entity\Role $role
     */
    public function removeRole(\AppBundle\Entity\Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set scope
     *
     * @param string $scope
     *
     * @return Person
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
