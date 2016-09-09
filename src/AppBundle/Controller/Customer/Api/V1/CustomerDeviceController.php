<?php

namespace AppBundle\Controller\Customer\Api\V1;

use AppBundle\DBAL\EnumCustomerDeviceHistoryActionType;
use AppBundle\Entity\CustomerDevice;
use AppBundle\Entity\CustomerDeviceHistory;
use AppBundle\Form\Customer\Api\V1\CustomerDeviceType;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomerController
 * @Route("customer_device")
 */
class CustomerDeviceController extends Controller
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
        $customerDevice = new CustomerDevice();
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(CustomerDeviceType::class, $customerDevice);
        $form->submit($data);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $customerDeviceEntity = $this->getDoctrine()->getRepository('AppBundle:CustomerDevice')
                ->findOneBy(
                    [
                        'deviceType' => $form->get('deviceType')->getData(),
                        'deviceUuid' => $form->get('deviceUuid')->getData(),
                    ]
                );
            if ($customerDeviceEntity instanceof CustomerDevice) {
                if ($form->has('latitude')) {
                    $customerDeviceEntity->setLatitude($form->get('latitude')->getData());
                }
                if ($form->has('longitude')) {
                    $customerDeviceEntity->setLongitude($form->get('longitude')->getData());
                }
                if ($form->has('notificationToken')) {
                    $customerDeviceEntity->setNotificationToken($form->get('notificationToken')->getData());
                }

                $em->persist($customerDeviceEntity);
            } else {
                $em->persist($customerDevice);
            }
            
            $customerDeviceHistory = new CustomerDeviceHistory();
            $customerDeviceHistory
                ->setAction(EnumCustomerDeviceHistoryActionType::ENUM_CREATE)
                ->setCustomerDevice($customerDeviceEntity)
                ->setDateTime(new \DateTime())
                ->setStatus(true);
            $em->persist($customerDeviceHistory);

            $em->flush();

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return new Response($this->get('jms_serializer')->serialize($form->getErrors(), 'json'), Response::HTTP_BAD_REQUEST);
    }
}
