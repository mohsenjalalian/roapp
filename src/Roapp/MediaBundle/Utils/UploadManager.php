<?php

namespace Roapp\MediaBundle\Utils;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Session;

class UploadManager
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $temporaryDirectory;

    /**
     * @var string
     */
    private $permanentDirectory;

    /**
     * @var array
     */
    private $uploads;

    public function __construct(
        Session $session,
        $temporaryDirectory,
        $permanentDirectory,
        $uploads
    ) {
        $this->session = $session;
        $this->permanentDirectory = $permanentDirectory;
        $this->temporaryDirectory = $temporaryDirectory;
        $this->uploads = $uploads;
    }

    public function upload(UploadedFile $file, $directory)
    {
        $fs = new Filesystem();

        $tempDirectoryPath = $this->getTemporaryPath($directory);

        if (!$fs->exists($tempDirectoryPath)) {
            $fs->mkdir($tempDirectoryPath);
        }

        $newfileName = $this->getFileName($file);
        $file->move($tempDirectoryPath, $newfileName);

        return $newfileName;
    }

    public function getTemporaryPath($directory)
    {
        return sprintf(
            "%s/%s/%s",
            $this->temporaryDirectory,
            $this->session->getId(),
            $directory
        );
    }

    public function getPermanentPath($directory)
    {
        return sprintf(
            "%s/%s/%s",
            $this->permanentDirectory,
            $this->session->getId(),
            $directory
        );
    }

    public function getFileName(UploadedFile $file)
    {
        return sprintf(
            "%s.%s",
            uniqid(),
            $file->getClientOriginalExtension()
        );
    }

    public function clear()
    {
        // @TODO implement clear method and create a command
    }

    public function getFilePath($directory, $fileName, $temp = true)
    {
        if ($temp) {
            $path = $this->getTemporaryPath($directory);
        } else {
            $path = $this->getPermanentPath($directory);
        }

        return $path.'/'.$fileName;
    }


    public function exists($directory, $fileName)
    {
        $fs = new Filesystem();
        $path = $this->getFilePath($directory, $fileName);

        return $fs->exists($path);

    }
    
    public function moveToPermanent(MediaFile $mediaFile, $directory)
    {
        $fs = new Filesystem();

        $permDirectoryPath = $this->getPermanentPath($directory);

        if (!$fs->exists($permDirectoryPath)) {
            $fs->mkdir($permDirectoryPath);
        }

        $mediaFile->move($permDirectoryPath, $mediaFile->getFilename());

        $file = new MediaFile(
            $this->getFilePath($directory, $mediaFile->getFilename(), false),
            false
        );
        
        return $file;
    }
}