<?php

namespace Roapp\MediaBundle\Utils;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaFile extends File
{
    private $isTemp;

    public function setIsTemp($isTemp) {
        $this->isTemp = $isTemp;
    }

    public function getIsTemp()
    {
        return $this->isTemp;
    }

    public function __construct($path, $isTemp = null, $checkPath = true)
    {
        if ($isTemp) {
            $this->isTemp = $isTemp;
        }

        parent::__construct($path, $checkPath);
    }
}