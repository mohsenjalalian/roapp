<?php
/**
 * Created by PhpStorm.
 * User: mesripour
 * Date: 12/20/16
 * Time: 12:25 PM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\AbstractInvoice;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * Class PeriodInvoiceRepository
 * @package AppBundle\Repository
 */
class PeriodInvoiceRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getTodayPeriodInvoice()
    {
        $from = new \DateTime();
        $until = new \DateTime();
        $from = $from->setTime(00, 00, 00);
        $until = $until->setTime(23, 59, 59);
        try {
            $periodInvoice = $this->createQueryBuilder('p')
                ->Where('p.createdAt >= :from')
                ->andwhere('p.createdAt <= :until')
                ->andWhere('p.status=:status')
                ->setParameter('from', $from)
                ->setParameter('until', $until)
                ->setParameter('status', AbstractInvoice::STATUS_UNPAID)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $periodInvoice = null;
        }

        return $periodInvoice;
    }
}
