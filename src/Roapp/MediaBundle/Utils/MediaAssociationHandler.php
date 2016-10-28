<?php

namespace Roapp\MediaBundle\Utils;

use AppBundle\Entity\Media;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Roapp\MediaBundle\Annotation\UploadableField;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Roapp\MediaBundle\Utils\MediaFile;

class MediaAssociationHandler
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \Roapp\MediaBundle\Utils\UploadManager
     */
    private $uploadManager;

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $accessor;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $fs;


    public function __construct(Container $container)
    {
        $this->reader = $container->get('annotation_reader');
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
        $this->uploadManager = $container->get('roapp_media.upload_manager');
        $this->accessor = $container->get('property_accessor');
        $this->fs = $container->get('filesystem');
    }
    
    public function handle(&$entity, \ReflectionProperty $reflectionProperty) {

        /** @var UploadableField $propertyAnnotation */
        $propertyAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, UploadableField::class);

        if ($propertyAnnotation) {
            $associationMediaPropertyName = $propertyAnnotation->getMappedAttribute();

            if (empty($associationMediaPropertyName)) {
                throw new \Exception('mapped attribute must not be null');
            }

            if (empty($propertyAnnotation->getMediaName())) {
                throw new \Exception('media name must not be null');
            }

            /** @var ClassMetadata $classMetaDate */
            $classMetaData = $this->entityManager->getClassMetadata($reflectionProperty->class);
            $associationMapping = $classMetaData->getAssociationMapping($associationMediaPropertyName);
            $hasOne = (boolean) ($associationMapping['type'] & ClassMetadataInfo::TO_ONE);
            $mediaName = $propertyAnnotation->getMediaName();
            $propertyName = $reflectionProperty->getName();
            if ($hasOne) {
                $this->hasOneHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName);
            } else {
                $this->hasManyHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName);
            }

            $this->entityManager->flush();
        }
    }

    private function hasOneHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName)
    {
        $mediaUploads = $this->accessor->getValue($entity, $propertyName);
        $mediaAssociation = $this->accessor->getValue($entity, $associationMediaPropertyName);

        dump($mediaUploads);
        dump($mediaAssociation);
    }

    private function hasManyHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName)
    {
        /** @var ArrayCollection $mediaUploads */
        $mediaUploads = $this->accessor->getValue($entity, $propertyName);
        $mediaUploadsIterator = $mediaUploads->getIterator();

        $newMedias = new ArrayCollection();
        $oldMedias = new ArrayCollection();

        while ($mediaUploadsIterator->valid()) {
            $media = $mediaUploadsIterator->current();
            if ($media instanceof MediaFile) {
                /** @var MediaFile $media */
                $mediaEntity = new Media();
                $mediaEntity->setCreatedAt(new \DateTime());
                if ($media->getIsTemp()) {
                    $file = $this->uploadManager->moveToPermanent($media, $mediaName);
                    $mediaEntity->setFile($file);
                } else {
                    $mediaEntity->setFile($media);
                }
                $mediaEntity->setName($media->getFilename());
                $mediaEntity->setUpdatedAt($mediaEntity->getCreatedAt());
                $this->entityManager->persist($mediaEntity);
                
                $newMedias->add($mediaEntity);
            } else {
                $oldMedias->add($media);
            }

            $mediaUploadsIterator->next();
        }

        /** @var PersistentCollection $mediaAssociation */
        $mediaAssociation = $this->accessor->getValue($entity, $associationMediaPropertyName);
        /** @var \Iterator $mediaAssociationIterator */
        $mediaAssociationIterator = $mediaAssociation->getIterator();
        while ($mediaAssociationIterator->valid()) {
            $mediaEntity = $mediaAssociationIterator->current();
            if (!$oldMedias->contains($mediaEntity)) {
                $mediaAssociation->removeElement($mediaEntity);
            }
        }

        $newMediasIterator = $newMedias->getIterator();
        while ($newMediasIterator->valid()) {
            $newMediaEntity = $newMediasIterator->current();
            $mediaAssociation->add($newMediaEntity);
            $newMediasIterator->next();
        }
    }
}