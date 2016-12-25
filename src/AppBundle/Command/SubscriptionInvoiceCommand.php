<?php

namespace AppBundle\Command;

use AppBundle\Entity\AbstractInvoice;
use AppBundle\Entity\BusinessUnit;
use AppBundle\Entity\SubscriptionInvoice;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SubscriptionInvoiceCommand
 * @package AppBundle\Command
 */
class SubscriptionInvoiceCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:invoice_subscription:publish')
            ->setDescription('publish invoice subscription for each businessUnit');
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
            /** @var BusinessUnit $businessUnit */
            $createdAt = $businessUnit->getCreatedAt()
                ->format('Y-m-d');
            $dueDate = date('Y-m-d', strtotime("+2 months", strtotime($createdAt)));
            $currentDate = date('Y-m-d');
            if ($currentDate > $dueDate) {
                $subscriptionInvoice = new SubscriptionInvoice();
                $subscriptionInvoice->setStatus(AbstractInvoice::STATUS_UNPAID);
                $subscriptionInvoice->setBusinessUnit($businessUnit);
                $subscriptionInvoice->setPrice("20000");

                $entityManager->persist($subscriptionInvoice);
                $entityManager->flush();
            }
        }
    }
}
