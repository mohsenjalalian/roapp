<?php

namespace AppBundle\Command;

use AppBundle\Entity\AbstractInvoice;
use AppBundle\Entity\PeriodInvoice;
use AppBundle\Entity\Shipment;
use AppBundle\Repository\ShipmentRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PeriodInvoiceCommand
 * @package AppBundle\Command
 */
class PeriodInvoiceCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:period_invoice:publish')
            ->setDescription('create invoice for each businessUnit');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $businessUnits = $entityManager->getRepository("AppBundle:BusinessUnit")
            ->findAll();
        foreach ($businessUnits as $businessUnit) {
            /** @var ShipmentRepository $shipmentRepository */
            $shipmentRepository = $entityManager->getRepository("AppBundle:Shipment");
            $shipments = $shipmentRepository
                ->getShipmentsPerBusinessUnit($businessUnit);
            if ($shipments) {
                $periodInvoice = new PeriodInvoice();
                $totalPrice = 0;
                foreach ($shipments as $shipment) {
                    /** @var Shipment $shipment */
                    $price = $shipment->getPrice();
                    $totalPrice = $totalPrice + intval($price);
                    $periodInvoice->addShipment($shipment);
                }
                $periodInvoice->setStatus(AbstractInvoice::STATUS_UNPAID);
                $periodInvoice->setPrice($totalPrice);

                $entityManager->persist($periodInvoice);
                $entityManager->flush();
            } else {
                continue;
            }
        }
    }
}
