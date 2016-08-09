<?php

namespace AppBundle\Controller\Customer\Api\V1;

use AppBundle\Entity\Customer;
use AppBundle\Entity\CustomerDevice;
use AppBundle\Form\Customer\Api\V1\VerificationRequestType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VerificationController
 * 
 * @Route(path="/verification")
 * 
 * @package AppBundle\Controller\Customer\Api\V1
 */
class VerificationController extends Controller
{
    /**
     * @Route(path="/request")
     */
    public function verification_request(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(VerificationRequestType::class);
        $form->submit($data);

        if ($form->isValid()) {
            $customerDevice = $this->getDoctrine()
                ->getRepository('AppBundle:CustomerDevice')
                ->findOneBy(
                    [
                        'deviceUuid' => $form->get('deviceUuid')->getData(),
                        'deviceType' => $form->get('deviceType')->getData(),
                    ]
                );

            if (!$customerDevice instanceof CustomerDevice) {
                return new Response('device not found', Response::HTTP_NOT_FOUND);
            }

            $customer = $this->getDoctrine()
                ->getRepository('AppBundle:Customer')
                ->findOneBy(
                    [
                        'phone' => $form->get('phone')->getData(),
                    ]
                );

            if (!$customer instanceof Customer) {
                $customer = new Customer();
                $customer->setPhone($form->get('phone')->getData());
                $this->getDoctrine()
                    ->getManager()
                    ->persist($customer);
            }

            $customerDevice->setCustomer($customer);
            $customerDevice->setPhoneVerificationSent(new \DateTime());
            $customerDevice->setPhoneVerificationCode(rand(100000, 999999));
            $customerDevice->setPhoneVerificationStatus(false);

            $this->getDoctrine()
                ->getManager()
                ->persist($customerDevice);

            return new Response($this->get('jms_serializer')->serialize($customerDevice, 'json'));
        }

        return new Response($this->get('jms_serializer')->serialize($form->getErrors(), 'json'), Response::HTTP_BAD_REQUEST);
    }
}
