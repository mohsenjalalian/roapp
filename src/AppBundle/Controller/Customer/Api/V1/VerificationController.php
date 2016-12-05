<?php

namespace AppBundle\Controller\Customer\Api\V1;

use AppBundle\DBAL\EnumPersonDeviceHistoryActionType;
use AppBundle\Entity\Customer;
use AppBundle\Entity\PersonDevice;
use AppBundle\Entity\PersonDeviceHistory;
use AppBundle\Form\Customer\Api\V1\VerificationReportType;
use AppBundle\Form\Customer\Api\V1\VerificationRequestType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VerificationController
 * @Route(path="/verification")
 * @package AppBundle\Controller\Customer\Api\V1
 */
class VerificationController extends Controller
{
    /**
     * Verify login form
     *
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @Route(path="/request")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function verificationRequest(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(VerificationRequestType::class);
        $form->submit($data);
        if ($form->isValid()) {
            $customerDevice = $this->getDoctrine()
                ->getRepository('AppBundle:PersonDevice')
                ->findOneBy(
                    [
                        'deviceUuid' => $form->get('deviceUuid')->getData(),
                        'deviceType' => $form->get('deviceType')->getData(),
                    ]
                );

            if (!$customerDevice instanceof PersonDevice) {
                return new Response('device not found', Response::HTTP_NOT_FOUND);
            }

            $customer = $this->getDoctrine()
                ->getRepository('AppBundle:Customer')
                ->findOneBy(
                    [
                        'phone' => $form->get('phone')->getData(),
                    ]
                );

            $em = $this->getDoctrine()
                ->getManager();

            if (!$customer instanceof Customer) {
                $customer = new Customer();
                $customer->setPhone($form->get('phone')->getData());
                $em->persist($customer);
            }

            $randomVerificationNumber = rand(100000, 999999);
            $customerDevice->setPerson($customer);
            $customerDevice->setPhoneVerificationSent(new \DateTime());
            $customerDevice->setPhoneVerificationCode($randomVerificationNumber);
            $customerDevice->setPhoneVerificationStatus(false);
            $em->persist($customerDevice);

            $qb = $this->getDoctrine()
                ->getRepository('AppBundle:PersonDeviceHistory')
                ->createQueryBuilder('history');
            $now = date('Y-m-d H:m:s', time());
            $yesterday =  date('Y-m-d H:m:s', time() - 60 * 60 * 24);
            $lastFiveVerificationRequest = $qb
                ->select('history.id')
                ->where('history.dateTime BETWEEN :yesterday AND :now')
                ->setParameter('yesterday', $yesterday)
                ->setParameter('now', $now)
                ->andWhere('history.status = TRUE')
                ->getQuery()
                ->getResult();

            $customerDeviceHistory = new PersonDeviceHistory();

            if (count($lastFiveVerificationRequest) > 4) {
                $customerDeviceHistory
                    ->setAction(EnumPersonDeviceHistoryActionType::ENUM_VERIFY)
                    ->setCustomerDevice($customerDevice)
                    ->setDateTime(new \DateTime())
                    ->setData(
                        [
                            "message" => $this->get('translator')
                                ->trans('You reached maximum verification request number.'),
                        ]
                    )
                    ->setStatus(false);
                $em->persist($customerDeviceHistory);
                $em->flush();

                return new JsonResponse(
                    [
                        "error" => true,
                        "message" => $this->get('translator')->trans('You reached maximum verification request number.'),
                    ]
                );
            }

            $customerDeviceHistory
                ->setAction(EnumPersonDeviceHistoryActionType::ENUM_VERIFY)
                ->setPersonDevice($customerDevice)
                ->setDateTime(new \DateTime())
                ->setData(
                    [
                        "message" => $this->get('translator')
                            ->trans('Sending verification sms'),
                    ]
                )
                ->setStatus(true);
            $em->persist($customerDeviceHistory);

            $em->flush();

            $this->get('app.sms')->send(
                $form->get('phone')->getData(),
                $this->get('translator')->trans('Your verification code is:').$randomVerificationNumber
            );

            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        return new Response($this->get('jms_serializer')->serialize($form->getErrors(), 'json'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Report verification code
     *
     * @Route(path="/report")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @param Request $request
     *
     * @return mixed
     */
    public function verificationReport(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(VerificationReportType::class);
        $form->submit($data);

        if ($form->isValid()) {
            $customerDevice = $this->getDoctrine()
                ->getRepository('AppBundle:PersonDevice')
                ->findOneBy(
                    [
                        'deviceUuid' => $form->get('deviceUuid')->getData(),
                        'deviceType' => $form->get('deviceType')->getData(),
                        'phoneVerificationCode' => $form->get('verificationCode')->getData(),
                    ]
                );

            $em = $this->getDoctrine()
                ->getManager();

            if (!$customerDevice instanceof PersonDevice) {
                $customerDeviceHistory = new PersonDeviceHistory();
                $customerDeviceHistory
                    ->setAction(EnumPersonDeviceHistoryActionType::ENUM_REPORT)
                    ->setCustomerDevice($customerDevice)
                    ->setDateTime(new \DateTime())
                    ->setData(
                        [
                            "message" => $this->get('translator')
                                ->trans('Verification report: Customer device not found.'),
                        ]
                    )
                    ->setStatus(false);
                $em->persist($customerDeviceHistory);
                $em->flush();

                return new Response('invalid verification', Response::HTTP_NOT_FOUND);
            }

            $token = $this->get('app.token_generator')
                ->generate($form->get('deviceUuid')->getData());
            $customerDevice->setPhoneVerificationStatus(true)
                ->setDeviceToken($token);
            $em->persist($customerDevice);
            $customerDeviceHistory = new PersonDeviceHistory();
            $customerDeviceHistory
                ->setAction(EnumPersonDeviceHistoryActionType::ENUM_REPORT)
                ->setPersonDevice($customerDevice)
                ->setDateTime(new \DateTime())
                ->setData(
                    [
                        "message" => $this->get('translator')
                            ->trans('Verification report: Customer Verified Successfully.'),
                    ]
                )
                ->setStatus(true);
            $em->persist($customerDeviceHistory);
            $em->flush();

            return new JsonResponse(
                [
                    'token' => $token,
                ]
            );
        }

        return new Response($this->get('jms_serializer')->serialize($form->getErrors(), 'json'), Response::HTTP_BAD_REQUEST);
    }
}
