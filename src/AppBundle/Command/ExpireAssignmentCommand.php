<?php

namespace AppBundle\Command;

use AppBundle\Entity\ShipmentAssignment;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExpireAssignmentCommand
 * @package AppBundle\Command
 */
class ExpireAssignmentCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:expire_assignment:check')
            ->setDescription('fetch expire assignments and change shipment status,driver status,shipment assignment status ');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $shipmentAssignments = $entityManager->getRepository("AppBundle:ShipmentAssignment")->findBy(['status' => ShipmentAssignment::STATUS_WAITING]);
        $expirationFlag = false;
        $totalExpirationFind = 0;
        foreach ($shipmentAssignments as $shipmentAssignment) {
            /** @var ShipmentAssignment $shipmentAssignment */
            $expireTime = $shipmentAssignment->getExpireTime();
            $currentTime = new \DateTime();
            if ($currentTime > $expireTime) { // time over
                $expirationFlag = true ;
                $totalExpirationFind = $totalExpirationFind + 1;
                $this->getContainer()->get('app.shipment_assignment')->timeOutAction($shipmentAssignment);
            }
        }
        if ($expirationFlag) {
            $output->writeln($totalExpirationFind.' shipment assignment expiration find');
        } else {
            $output->writeln('there is no any shipment assignment expiration');
        }
    }
}
