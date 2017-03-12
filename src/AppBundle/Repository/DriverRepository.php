<?php

namespace AppBundle\Repository;

use AppBundle\Entity\BusinessUnit;
use AppBundle\Entity\Driver;
use AppBundle\Entity\ShipmentAssignment;
use AppBundle\Entity\Shipment;
use AppBundle\Utils\AssignmentShipment;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * DriverRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DriverRepository extends EntityRepository
{
    // check shipment rejected by some driver or no
    /**
     * @param Shipment $shipment
     * @return array
     */
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
            return $banDriver = [];
        }
    }

    /**
     * @param int    $id
     * @param string $currentPassword
     * @return \AppBundle\Entity\Driver[]|array
     */
    public function validationCurrentPassword($id, $currentPassword)
    {
        $result = $this
            ->findOneBy(
                [
                    'id' => $id,
                    'password' => $currentPassword,
                ]
            )
        ;

        return $result;
    }

    /**
     * @param BusinessUnit $businessUnit
     * @param integer      $status
     * @param null         $driverId
     * @return array $drivers
     */
    public function businessUnitDriver(BusinessUnit $businessUnit, $status, $driverId = null)
    {
        $drivers = $this->getEntityManager()->getRepository('AppBundle:Driver')
            ->createQueryBuilder('driver')
            ->join('driver.businessUnit', 'businessUnit')
            ->where('driver.businessUnit = :businessUnit')
            ->andWhere('driver.status = :status')
            ->setParameter('businessUnit', $businessUnit)
            ->setParameter('status', $status);

        if ($driverId != null) {
            $drivers = $drivers->orWhere('driver.id = :driver_id')
                ->setParameter('driver_id', $driverId);
        }

        return $drivers;
    }

    /**
     * @param Driver $driver
     * @return bool
     */
    public function isOpenTaskExist(Driver $driver)
    {
        $assignment = $this->getEntityManager()->getRepository('AppBundle:ShipmentAssignment')
            ->createQueryBuilder('assignment')
            ->join('assignment.driver', 'driver')
            ->join('assignment.shipment', 'shipment')
            ->where('assignment.driver = :driver')
            ->andWhere('shipment.status IN (:status)')
            ->setParameter('driver', $driver)
            ->setParameter(
                'status',
                [
                    Shipment::STATUS_ON_PICK_UP,
                    Shipment::STATUS_PICKED_UP,
                    Shipment::STATUS_ON_DELIVERY,
                    Shipment::STATUS_DELIVERED,
                ]
            )
            ->getQuery()
            ->getResult()
        ;
        if ($assignment instanceof AssignmentShipment) {
            return true;
        } else {
            return false;
        }
    }
}
