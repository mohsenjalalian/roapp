<?php

namespace AppBundle\Command;

use AppBundle\Entity\BusinessType;
use AppBundle\Entity\Shipment;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\NoResultException;

/**
 * Class BusinessTypeReloadCommand
 * @package AppBundle\Command
 */
class BusinessTypeReloadCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:business_type_reload_command')
            ->setDescription('Reload BusinessType');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $types = $this->getContainer()->get('doctrine')->getManager()->getClassMetadata(Shipment::class)->subClasses;
        foreach ($types as $type) {
            try {
                /** @var EntityManager $em */
                $businessTypeEntity = $entityManager
                    ->getRepository('AppBundle:BusinessType')
                    ->createQueryBuilder('business_type')
                    ->where('business_type.name = :name')->setParameter('name', $type)
                    ->getQuery()
                    ->getSingleResult();
            } catch (NoResultException $e) {
                $businessTypeEntity = new BusinessType();
                $businessTypeEntity->setName($type);
                $entityManager->persist($businessTypeEntity);
                $entityManager->flush();
            }
        }
    }
}
