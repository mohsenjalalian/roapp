<?php

namespace Roapp\MediaBundle\EventListener;

use AppBundle\Entity\Shipment;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Roapp\MediaBundle\Annotation\UploadableField;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Roapp\MediaBundle\Utils\HasManyHandler;
use Roapp\MediaBundle\Utils\MediaAssociationHandler;

class UploadSubscriber implements EventSubscriber
{
    /**
     * @var MediaAssociationHandler
     */
    private $mediaAssociationHandler;

    public function __construct(MediaAssociationHandler $mediaAssociationHandler)
    {
        $this->mediaAssociationHandler = $mediaAssociationHandler;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
            'postLoad',
        );
    }

    public function postLoad(LifecycleEventArgs $args)
    {

    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $reflectionClass = new \ReflectionClass($entity);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $this->mediaAssociationHandler->handle($entity, $reflectionProperty);
        }
    }
    
    private function handleHasOne($entity, $mediaPropertyName, $reflectionProperty)
    {
        
    }
    
    private function handleHasMany()
    {
        
    }
}