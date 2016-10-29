<?php

namespace Roapp\MediaBundle\Controller;

use AppBundle\Entity\Media;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/{prefix}/upload_{mediaName}/upload", name="_upload", requirements={"prefix"=".+"})
     * @Method({"POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($prefix, $mediaName, Request $request)
    {
        $file = $request->files->get('file');

        $uploads = $this->getParameter('roapp_media.uploads');

        if (!isset($uploads[$mediaName])) {
            return new Response("$mediaName media name is not available", 404);
        }

        if ($uploads[$mediaName]['path'] !== $prefix) {
            return new Response("You have not access to this media", 403);
        }

        if (!$file instanceof UploadedFile) {
            return new Response('The file should not be empty', 400);
        }

        $uploadManager = $this->get('roapp_media.upload_manager');

        $tempFileName = $uploadManager->upload($file, $mediaName);

        return new Response($tempFileName);
    }

    /**
     * @Route("/{prefix}/upload_{mediaName}/link/{media}", name="_link", requirements={"prefix"=".+"})
     * @Method({"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function linkAction($prefix, $mediaName, Media $media,Request $request)
    {
        $uploads = $this->getParameter('roapp_media.uploads');

        if (!isset($uploads[$mediaName])) {
            return new Response("$mediaName media name is not available", 404);
        }

        if ($uploads[$mediaName]['path'] !== $prefix) {
            return new Response("You have not access to this media", 403);
        }
        
        $uploadManager = $this->get('roapp_media.upload_manager');
        $url = $uploadManager->generateAbsoluteUrl($media);

        return new Response($url);
    }
    
    
}
