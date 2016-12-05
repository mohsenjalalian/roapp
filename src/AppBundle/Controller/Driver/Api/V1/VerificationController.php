<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\DBAL\EnumPersonDeviceHistoryActionType;
use AppBundle\Entity\Driver;
use AppBundle\Entity\PersonDevice;
use AppBundle\Entity\PersonDeviceHistory;
use AppBundle\Form\Driver\Api\V1\VerificationReportType;
use AppBundle\Form\Driver\Api\V1\VerificationRequestType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VerificationController
 * @Route(path="/verification")
 * @package AppBundle\Controller\Driver\Api\V1
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
            $driverDevice = $this->getDoctrine()
                ->getRepository('AppBundle:PersonDevice')
                ->findOneBy(
                    [
                        'deviceUuid' => $form->get('deviceUuid')->getData(),
                        'deviceType' => $form->get('deviceType')->getData(),
                    ]
                );

            if (!$driverDevice instanceof PersonDevice) {
                return new Response('device not found', Response::HTTP_NOT_FOUND);
            }

            $driver = $this->getDoctrine()
                ->getRepository('AppBundle:Driver')
                ->findOneBy(
                    [
                        'phone' => $form->get('phone')->getData(),
                    ]
                );

            $em = $this->getDoctrine()
                ->getManager();

            if (!$driver instanceof Driver) {
                $driver = new Driver();
                $driver->setPhone($form->get('phone')->getData());
                $em->persist($driver);
            }

            $randomVerificationNumber = rand(100000, 999999);
            $driverDevice->setPerson($driver);
            $driverDevice->setPhoneVerificationSent(new \DateTime());
            $driverDevice->setPhoneVerificationCode($randomVerificationNumber);
            $driverDevice->setPhoneVerificationStatus(false);
            $em->persist($driverDevice);

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

            $driverDeviceHistory = new PersonDeviceHistory();

            if (count($lastFiveVerificationRequest) > 4) {
                $driverDeviceHistory
                    ->setAction(EnumPersonDeviceHistoryActionType::ENUM_VERIFY)
                    ->setPersonDevice($driverDevice)
                    ->setDateTime(new \DateTime())
                    ->setData(
                        [
                            "message" => $this->get('translator')
                                ->trans('You reached maximum verification request number.'),
                        ]
                    )
                    ->setStatus(false);
                $em->persist($driverDeviceHistory);
                $em->flush();

                return new JsonResponse(
                    [
                        "error" => true,
                        "message" => $this->get('translator')->trans('You reached maximum verification request number.'),
                    ]
                );
            }

            $driverDeviceHistory
                ->setAction(EnumPersonDeviceHistoryActionType::ENUM_VERIFY)
                ->setPersonDevice($driverDevice)
                ->setDateTime(new \DateTime())
                ->setData(
                    [
                        "message" => $this->get('translator')
                            ->trans('Sending verification sms'),
                    ]
                )
                ->setStatus(true);
            $em->persist($driverDeviceHistory);

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
            $driverDevice = $this->getDoctrine()
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

            if (!$driverDevice instanceof PersonDevice) {
                $driverDeviceHistory = new PersonDeviceHistory();
                $driverDeviceHistory
                    ->setAction(EnumPersonDeviceHistoryActionType::ENUM_REPORT)
                    ->setPersonDevice($driverDevice)
                    ->setDateTime(new \DateTime())
                    ->setData(
                        [
                            "message" => $this->get('translator')
                                ->trans('Verification report: Person device not found'),
                        ]
                    )
                    ->setStatus(false);
                $em->persist($driverDeviceHistory);
                $em->flush();

                return new Response('invalid verification', Response::HTTP_BAD_REQUEST);
            }

            $token = $this->get('app.token_generator')
                ->generate($form->get('deviceUuid')->getData());
            $driverDevice->setPhoneVerificationStatus(true)
                ->setDeviceToken($token);
            $em->persist($driverDevice);
            $driverDeviceHistory = new PersonDeviceHistory();
            $driverDeviceHistory
                ->setAction(EnumPersonDeviceHistoryActionType::ENUM_REPORT)
                ->setPersonDevice($driverDevice)
                ->setDateTime(new \DateTime())
                ->setData(
                    [
                        "message" => $this->get('translator')
                            ->trans('Verification report: Person Verified Successfully'),
                    ]
                )
                ->setStatus(true);
            $em->persist($driverDeviceHistory);

            $em->flush();

            return new Response(
                $token
            );
        }

        return new Response($this->get('jms_serializer')->serialize($form->getErrors(), 'json'), Response::HTTP_BAD_REQUEST);
    }
}
