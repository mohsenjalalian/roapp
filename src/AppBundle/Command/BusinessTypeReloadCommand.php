<?php

namespace AppBundle\Command;

use AppBundle\Entity\BusinessType;
use AppBundle\Entity\Shipment;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\NoResultException;
use AppBundle\Utils\BusinessTypeBundleInterface;

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
            ->setName('app:business_type:reload')
            ->setDescription('Reload BusinessType');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        global $kernel;
        /** @var BusinessTypeBundleInterface $bundle */
        foreach ($kernel->getBundles() as $bundle) {
            if ($bundle instanceof BusinessTypeBundleInterface) {
                $entityNameSpace = $bundle->getShipmentEntityNamespace();
                $formNameSpace = $bundle->getShipmentFormNamespace();
                $businessTypeName = $bundle->getBusinessTypeName();
                try {
                    /** @var EntityManager $em */
                    $businessTypeEntity = $entityManager
                        ->getRepository('AppBundle:BusinessType')
                        ->createQueryBuilder('business_type')
                        ->where('business_type.bundleNamespace = :namespace')->setParameter('namespace', get_class($bundle))
                        ->getQuery()
                        ->getSingleResult();
                } catch (NoResultException $e) {
                    $businessTypeEntity = new BusinessType();
                    $businessTypeEntity->setBundleNamespace(get_class($bundle));
                }
                $businessTypeEntity->setEntityNamespace($entityNameSpace);
                $businessTypeEntity->setFormNamespace($formNameSpace);
                $businessTypeEntity->setName($businessTypeName);
                $entityManager->persist($businessTypeEntity);
                $entityManager->flush();
            }
        }
    }
}
