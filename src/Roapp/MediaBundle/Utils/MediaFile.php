<?php

namespace Roapp\MediaBundle\Utils;

use AppBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\File;

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

    public function getIsTemp()
    {
        return $this->isTemp;
    }

    public function __construct($path, $isTemp = null, Media $mediaEntity = null ,$checkPath = true)
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
    public function getMediaEntity() {
        return $this->mediaEntity;
    }

    /**
     * @param \AppBundle\Entity\Media $mediaEntity
     * @return $this
     */
    public function setMediaEntity(Media $mediaEntity) {
        $this->mediaEntity = $mediaEntity;
        
        return $this;
    }
}