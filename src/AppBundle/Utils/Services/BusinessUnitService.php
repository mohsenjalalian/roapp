<?php

namespace AppBundle\Utils\Services;

use AppBundle\Entity\BusinessType;
use AppBundle\Entity\BusinessUnit;

/**
 * Class BusinessTypeService
 * @package AppBundle\Utils\Services
 */
class BusinessUnitService
{
    /**
     * @param \AppBundle\Entity\BusinessType $businessType
     * @return BusinessUnit
     */
    public function businessUnitFactory(BusinessType $businessType)
    {
        $businessUnitClass = $businessType->getBusinessUnitEntity();
        /** @var BusinessUnit $businessUnit */
        $businessUnit = new $businessUnitClass();

        return $businessUnit;
    }
}
