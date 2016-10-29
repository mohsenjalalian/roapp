<?php

namespace Roapp\MediaBundle\Form;

use AppBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Roapp\MediaBundle\Utils\MediaFile;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Validator\Tests\Fixtures\Entity;

class UploadTransformer implements DataTransformerInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;

    /**
     * @var RoappImageType
     */
    private $form;

    public function __construct(Container $container, RoappImageType $form)
    {
        $this->container = $container;
        $this->form = $form;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (is_array($value)) {
            $arrayValue = $value;
        } else {
            throw new \Exception('Value must array or ArrayCollection');
        }

        $finalValueArray = [];
        /** @var MediaFile $mediaFile */
        foreach ($arrayValue as $mediaFile) {
            if ($mediaFile->getIsTemp()) {
                $finalValueArray[] = $mediaFile->getFilename();
            } else {
                $finalValueArray[] = $mediaFile->getMediaEntity()->getId();
            }
        }

        return implode(',', $finalValueArray);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {

        $mediaName = $this->form->getMediaName();

        $uploadManager = $this->container->get('roapp_media.upload_manager');
        $fileIdentifiers = explode(',', $value);
        $fileCollection = new ArrayCollection();
        
        foreach ($fileIdentifiers as $fileIdentifier) {
            if (is_numeric($fileIdentifier)) {
                $mediaEntity = $this->container->get('doctrine.orm.default_entity_manager')
                    ->getRepository('AppBundle:Media')
                    ->find($fileIdentifier);

                if (!$mediaEntity instanceof Media) {
                    throw new \Exception('Invalid file identifier');
                }
                $fileCollection->add($mediaEntity);
            } elseif (preg_replace('/[^A-Za-z0-9 _ .-]/', '', $fileIdentifier)) {
                if ($uploadManager->exists($mediaName, $fileIdentifier)) {
                    $file = new MediaFile(
                        $uploadManager->getFilePath($mediaName, $fileIdentifier),
                        true
                    );

                    $fileCollection->add($file);
                } else {
                    throw new \Exception('File doesn\'t exist.');
                }
            } else {
                throw new \Exception('Invalid file identifier');
            }
        }

        return $fileCollection;
    }
}
