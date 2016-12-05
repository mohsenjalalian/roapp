<?php

namespace Roapp\MediaBundle\Utils;

use AppBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class MediaFile
 * @package Roapp\MediaBundle\Utils
 */
class MediaFile extends File
{
    /**
     * @var boolean|null
     */
    private $isTemp;

    /**
     * @var Media
     */
    private $mediaEntity;

    /**
     * @return bool|null
     */
    public function getIsTemp()
    {
        return $this->isTemp;
    }

    /**
     * MediaFile constructor.
     * @param string     $path
     * @param null       $isTemp
     * @param Media|NULL $mediaEntity
     * @param bool       $checkPath
     * @throws \Exception
     */
    public function __construct($path, $isTemp = null, Media $mediaEntity = null, $checkPath = true)
    {
        if (!is_null($isTemp)) {
            if ($isTemp == false) {
                if (!$mediaEntity) {
                    throw new \Exception('For permanent media file media entity is required');
                } else {
                    $this->mediaEntity = $mediaEntity;
                }
            }
            $this->isTemp = $isTemp;
        }

        parent::__construct($path, $checkPath);
    }

    /**
     * @return \AppBundle\Entity\Media
     */
    public function getMediaEntity()
    {
        return $this->mediaEntity;
    }

    /**
     * @param \AppBundle\Entity\Media $mediaEntity
     * @return $this
     */
    public function setMediaEntity(Media $mediaEntity)
    {
        $this->mediaEntity = $mediaEntity;

        return $this;
    }
}
