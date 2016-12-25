<?php

namespace AppBundle\Repository;

use AppBundle\Entity\BusinessUnit;
use AppBundle\Entity\Shipment;
use Doctrine\ORM\EntityRepository;

/**
 * ShipmentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ShipmentRepository extends EntityRepository
{
    /**
     * @param BusinessUnit $businessUnit
     * @return array
     */
    public function getShipmentsPerBusinessUnit(BusinessUnit $businessUnit)
    {
        $from = new \DateTime();
        $until = new \DateTime();
        $from = $from->setTime(00, 00, 00);
        $until = $until->setTime(23, 59, 59);
        $shipments =  $this->createQueryBuilder('s')
            ->select('s')
            ->join('s.ownerAddress', 'a')
            ->join('a.businessUnit', 'b')
            ->where('b.id=:bid')
            ->andWhere('s.createdAt >= :from')
            ->andwhere('s.createdAt <= :until')
            ->andWhere('s.customerDriver=:customer_driver')
            ->andWhere('s.status=:status')
            ->setParameter('bid', $businessUnit->getId())
            ->setParameter('from', $from)
            ->setParameter('until', $until)
            ->setParameter('customer_driver', false)
            ->setParameter('status', Shipment::STATUS_FINISH)
            ->getQuery()
            ->getResult();

        return $shipments;
    }
}
