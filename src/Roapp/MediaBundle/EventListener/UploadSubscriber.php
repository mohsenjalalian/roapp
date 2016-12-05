<?php

namespace Roapp\MediaBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Roapp\MediaBundle\Utils\HasManyHandler;
use Roapp\MediaBundle\Utils\MediaAssociationHandler;

/**
 * Class UploadSubscriber
 * @package Roapp\MediaBundle\EventListener
 */
class UploadSubscriber implements EventSubscriber
{
    /**
     * @var MediaAssociationHandler
     */
    private $mediaAssociationHandler;

    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * UploadSubscriber constructor.
     *
     * @param MediaAssociationHandler $mediaAssociationHandler
     * @param Reader                  $reader
     */
    public function __construct(MediaAssociationHandler $mediaAssociationHandler, Reader $reader)
    {
        $this->mediaAssociationHandler = $mediaAssociationHandler;
        $this->reader = $reader;
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

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @throws \Exception
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $reflectionClass = new \ReflectionClass($entity);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $this->mediaAssociationHandler->handle($entity, $reflectionProperty, 'load');
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args);
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @throws \Exception
     */
    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $reflectionClass = new \ReflectionClass($entity);
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $this->mediaAssociationHandler->handle($entity, $reflectionProperty, 'persist');
        }
    }
}
