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

    public function handle(&$entity, \ReflectionProperty $reflectionProperty, $mode = 'persist')
    {
        if (!in_array($mode, ['persist', 'load'])) {
            throw new \Exception('Invalid media association handler mode');
        }

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
                if ($mode == 'persist') {
                    $this->hasOnePersistHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName);
                } else {
                    $this->hasOneLoadHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName);
                }
            } else {
                if ($mode == 'persist') {

                    $this->hasManyPersistHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName);
                } else {
                    $this->hasManyLoadHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName);
                }
            }

            $this->entityManager->flush();
        }
    }

    private function hasOnePersistHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName)
    {
        $mediaUploads = $this->accessor->getValue($entity, $propertyName);
        $mediaAssociation = $this->accessor->getValue($entity, $associationMediaPropertyName);

    }

    private function hasManyPersistHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName)
    {

        $mediaUploads = $this->accessor->getValue($entity, $propertyName);
        /* @TODO please resolve mediaUploads value   */
        if($mediaUploads == null){
            $mediaUploads = [];
        }
//        $mediaUploadsIterator = $mediaUploads->getIterator();
        $newMedias = new ArrayCollection();
        $oldMedias = new ArrayCollection();
        foreach ($mediaUploads as $media) {
            if ($media instanceof MediaFile) {
                /** @var MediaFile $media */
                $mediaEntity = new Media();
                $mediaEntity->setCreatedAt(new \DateTime());
                $mediaEntity->setMediaName($mediaName);
                if ($media->getIsTemp()) {
                    $file = $this->uploadManager->moveToPermanent($media, $mediaName, $mediaEntity);
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
            $mediaAssociationIterator->next();
        }

        $newMediasIterator = $newMedias->getIterator();
        while ($newMediasIterator->valid()) {
            $newMediaEntity = $newMediasIterator->current();
            $mediaAssociation->add($newMediaEntity);
            $newMediasIterator->next();
        }
    }

    private function hasManyLoadHandler($entity, $mediaName, $propertyName, $associationMediaPropertyName)
    {
        /** @var ArrayCollection $mediaEntities */
        $mediaEntities = $this->accessor->getValue($entity, $associationMediaPropertyName);
        /** @var ArrayCollection $mediaFiles */
        $mediaFiles = $this->accessor->getValue($entity, $propertyName);
        
        if ($mediaFiles == null) {
            $mediaFiles = new ArrayCollection();
        }
        $mediaEntitiesIterator = $mediaEntities->getIterator();
        
        while ($mediaEntitiesIterator->valid()) {
            /** @var Media $mediaEntity */
            $mediaEntity = $mediaEntitiesIterator->current();
            $mediaFile = new MediaFile(
                $this->uploadManager->getFilePath($mediaName, $mediaEntity->getName(), false),
                false,
                $mediaEntity
            );
            $mediaFiles->add($mediaFile);

            $mediaEntitiesIterator->next();
        }

        $this->accessor->setValue($entity, $propertyName, $mediaFiles);
    }
}