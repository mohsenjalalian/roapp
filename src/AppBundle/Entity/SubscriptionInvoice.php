<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SubscriptionInvoice
 * @package AppBundle\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubscriptionInvoiceRepository")
 */
class SubscriptionInvoice extends AbstractInvoice
{
    /**
     * Many subscription invoice have One businessUnit.
     * @ManyToOne(targetEntity="AppBundle\Entity\BusinessUnit")
     * @JoinColumn(name="business_unit_id", referencedColumnName="id")
     */
    private $businessUnit;

    /**
     * Set businessUnit
     *
     * @param \AppBundle\Entity\BusinessUnit $businessUnit
     *
     * @return SubscriptionInvoice
     */
    public function setBusinessUnit(BusinessUnit $businessUnit = null)
    {
        $this->businessUnit = $businessUnit;

        return $this;
    }

    /**
     * Get businessUnit
     *
     * @return \AppBundle\Entity\BusinessUnit
     */
    public function getBusinessUnit()
    {
        return $this->businessUnit;
    }
}
