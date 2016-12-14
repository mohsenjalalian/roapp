<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Shipment;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\Common\Annotations\AnnotationReader;
use Roapp\RestaurantBundle\Entity\RestaurantShipment;

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

            $discriminatorMap['restaurant'] = RestaurantShipment::class;
            $metadata->setDiscriminatorMap($discriminatorMap);
        }
    }
}
