<?php

namespace Roapp\MediaBundle\Form;

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
        return "";
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
            if (preg_replace('/[^A-Za-z0-9 _ .-]/', '', $fileIdentifier)) {
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
                // @TODO implement media entity handling
            }
        }

        return $fileCollection;
    }
}
