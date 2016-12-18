<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Shipment;
use AppBundle\Utils\BusinessTypeBundleInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\Annotations\AnnotationReader;
use Roapp\RestaurantBundle\Entity\RestaurantShipment;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ShipmentDiscreminatorMapListener
 * @package AppBundle\EventListener
 */
class ShipmentDiscriminatorMapListener
{
    /**
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $metadata = $event->getClassMetadata();
        if ($metadata->name == Shipment::class) {
            $class = $metadata->getReflectionClass();
            if ($class === null) {
                $class = new \ReflectionClass($metadata->getName());
            }

            $reader = new AnnotationReader();
            $discriminatorMap = array();
            if (null !== $discriminatorMapAnnotation = $reader->getClassAnnotation($class, 'Doctrine\ORM\Mapping\DiscriminatorMap')) {
                $discriminatorMap = $discriminatorMapAnnotation->value;
            }

            global $kernel;
            /** @var BusinessTypeBundleInterface $bundle */
            foreach ($kernel->getBundles() as $bundle) {
                if ($bundle instanceof BusinessTypeBundleInterface) {
                    $bundleClass = get_class($bundle);
                    $paths = explode('\\', call_user_func(array($bundleClass, 'getShipmentEntityNamespace')));
                    $entityName = array_pop($paths);
                    $discriminatorMap[$entityName] = $bundle->getShipmentEntityNamespace();
                }
            }

            $metadata->setDiscriminatorMap($discriminatorMap);
        }
    }
}
