<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\Entity\Driver;
use AppBundle\Entity\Shipment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DriverController
 * @package AppBundle\Controller\Driver\Api\V1
 * @Route("/driver")
 */
class DriverController extends Controller
{
    /**
     * @Route("/change_status")
     * @param Request $request
     * @return JsonResponse
     */
    public function changeStatus(Request $request)
    {
        $data = json_decode($request->getContent());
        $driverId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $driver = $this->getDoctrine()
            ->getRepository("AppBundle:Driver")
            ->find($driverId);
        if ($driver) {
            if ($data->availabel) {
                $driver->setStatus(Driver::STATUS_FREE);
                $em->persist($driver);

                $em->flush();

                return new JsonResponse([], Response::HTTP_NO_CONTENT);
            } else {
                $driver->setStatus(Driver::STATUS_NOT_AVAILABLE);
                $em->persist($driver);

                $em->flush();

                return new JsonResponse([], Response::HTTP_NO_CONTENT);
            }
        } else {
           return new JsonResponse([],Response::HTTP_BAD_REQUEST);
        }
    }
}