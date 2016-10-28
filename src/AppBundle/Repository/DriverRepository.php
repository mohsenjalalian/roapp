<?php

namespace AppBundle\Repository;
use AppBundle\Entity\ShipmentAssignment;
use AppBundle\Entity\Shipment;
use Doctrine\ORM\EntityRepository;

/**
 * DriverRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DriverRepository extends EntityRepository
{
    // check shipment rejected by some driver or no
    public function filterDriverAction(Shipment $shipment)
    {
        $filterDriver = $this->getEntityManager()
            ->getRepository("AppBundle:ShipmentAssignment")
            ->findBy(
                [
                    'shipment' => $shipment->getId(),
                    'status' => ShipmentAssignment::STATUS_REJECTED,
                ]
            )
        ;
        if ($filterDriver) {
            foreach ($filterDriver as $value) {
                $banDriver[] = $value->getDriver()
                    ->getId();
            }

            return $banDriver;
        } else {
            return $banDriver=[];
        }
    }
}
