<?php

namespace Roapp\MediaBundle\Utils;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Entity\Media;
use Symfony\Component\Routing\RequestContext;

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

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $rootDir;

    public function __construct(
        Session $session,
        $temporaryDirectory,
        $permanentDirectory,
        $uploads,
        RequestStack $requestStack,
        RequestContext $requestContext,
        $rootDir
    ) {
        $this->session = $session;
        $this->permanentDirectory = $permanentDirectory;
        $this->temporaryDirectory = $temporaryDirectory;
        $this->uploads = $uploads;
        $this->requestStack = $requestStack;
        $this->requestContext = $requestContext;
        $this->rootDir = $rootDir;
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
            "%s/%s",
            $this->permanentDirectory,
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
    
    public function getMediaPathForEntity(Media $media)
    {
        return $this->getFilePath($media->getMediaName(), $media->getName(), false);
    }


    public function exists($directory, $fileName, $temp = true)
    {
        $fs = new Filesystem();
        $path = $this->getFilePath($directory, $fileName, $temp);

        return $fs->exists($path);
    }
    
    public function moveToPermanent(MediaFile $mediaFile, $directory, Media $mediaEntity)
    {
        $fs = new Filesystem();

        $permDirectoryPath = $this->getPermanentPath($directory);

        if (!$fs->exists($permDirectoryPath)) {
            $fs->mkdir($permDirectoryPath);
        }

        $mediaFile->move($permDirectoryPath, $mediaFile->getFilename());

        $file = new MediaFile(
            $this->getFilePath($directory, $mediaFile->getFilename(), false),
            false,
            $mediaEntity
        );
        
        return $file;
    }

    public function generateAbsoluteUrl(Media $media)
    {
        $realBasePath = realpath($this->rootDir.'/../web/');
        $realMediaPath = realpath($this->getMediaPathForEntity($media));
        $path = explode($realBasePath, $realMediaPath)[1];
        
        if (false !== strpos($path, '://') || '//' === substr($path, 0, 2)) {
            return $path;
        }

        if (!$request = $this->requestStack->getMasterRequest()) {
            if (null !== $this->requestContext && '' !== $host = $this->requestContext->getHost()) {
                $scheme = $this->requestContext->getScheme();
                $port = '';

                if ('http' === $scheme && 80 != $this->requestContext->getHttpPort()) {
                    $port = ':'.$this->requestContext->getHttpPort();
                } elseif ('https' === $scheme && 443 != $this->requestContext->getHttpsPort()) {
                    $port = ':'.$this->requestContext->getHttpsPort();
                }

                if ('/' !== $path[0]) {
                    $path = rtrim($this->requestContext->getBaseUrl(), '/').'/'.$path;
                }

                return $scheme.'://'.$host.$port.$path;
            }

            return $path;
        }

        if (!$path || '/' !== $path[0]) {
            $prefix = $request->getPathInfo();
            $last = strlen($prefix) - 1;
            if ($last !== $pos = strrpos($prefix, '/')) {
                $prefix = substr($prefix, 0, $pos).'/';
            }

            return $request->getUriForPath($prefix.$path);
        }

        return $request->getSchemeAndHttpHost().$path;
    }
}