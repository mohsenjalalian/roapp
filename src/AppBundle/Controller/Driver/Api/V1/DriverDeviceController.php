<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\DBAL\EnumPersonDeviceHistoryActionType;
use AppBundle\Entity\PersonDevice;
use AppBundle\Entity\PersonDeviceHistory;
use AppBundle\Form\Driver\Api\V1\DriverDeviceType;
use Doctrine\DBAL\Types\Type;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DriverDeviceController
 * @Route("driver_device")
 */
class DriverDeviceController extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @param Request $request
     * @return JsonResponse
     * @Route()
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $driverDevice = new PersonDevice();
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(DriverDeviceType::class, $driverDevice);;
        $form->submit($data);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $driverDeviceEntity = $this->getDoctrine()->getRepository('AppBundle:PersonDevice')
                ->findOneBy(
                    [
                        'deviceType' => $form->get('deviceType')->getData(),
                        'deviceUuid' => $form->get('deviceUuid')->getData(),
                    ]
                );
            if ($driverDeviceEntity instanceof PersonDevice) {
                if ($form->has('latitude')) {
                    $driverDeviceEntity->setLatitude($form->get('latitude')->getData());
                }
                if ($form->has('longitude')) {
                    $driverDeviceEntity->setLongitude($form->get('longitude')->getData());
                }
                if ($form->has('notificationToken')) {
                    $driverDeviceEntity->setNotificationToken($form->get('notificationToken')->getData());
                }

                $em->persist($driverDeviceEntity);
            } else {
                $em->persist($driverDevice);
            }
            
            $driverDeviceHistory = new PersonDeviceHistory();
            $driverDeviceHistory
                ->setAction(EnumPersonDeviceHistoryActionType::ENUM_CREATE)
                ->setPersonDevice($driverDeviceEntity)
                ->setDateTime(new \DateTime())
                ->setStatus(true);
            $em->persist($driverDeviceHistory);

            $em->flush();
            
            return new JsonResponse(null,Response::HTTP_NO_CONTENT);
        }

        return new Response($this->get('jms_serializer')->serialize($form->getErrors(), 'json'), Response::HTTP_BAD_REQUEST);
    }
}
