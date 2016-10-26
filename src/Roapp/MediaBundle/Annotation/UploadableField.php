<?php

namespace Roapp\MediaBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target("PROPERTY")
 */
class UploadableField
{
    /**
     * @Annotation\Required
     *
     * @var string
     */
    public $mappedAttribute;
    
    /**
     * @Annotation\Required
     *
     * @var string
     */
    public $mediaName;

    /**
     * @return string
     */
    public function getMappedAttribute()
    {
        return $this->mappedAttribute;
    }

    /**
     * @return string
     */
    public function getMediaName()
    {
        return $this->mediaName;
    }
}