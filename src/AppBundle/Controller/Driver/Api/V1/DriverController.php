<?php

namespace AppBundle\Controller\Driver\Api\V1;

use AppBundle\Entity\Driver;
use AppBundle\Entity\PersonDevice;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swift_Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DriverController
 * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
 * @package AppBundle\Controller\Driver\Api\V1
 * @Route("/driver")
 */
class DriverController extends Controller
{
    /**
     * @Route("/change_status")
     * @Method("POST")
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
            $isOpenTaskExist = $em->getRepository("AppBundle:Driver")->isOpenTaskExist($driver);
            if ($isOpenTaskExist) {
                if ($data->available) {
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
                return new JsonResponse([], Response::HTTP_FORBIDDEN);
            }
        } else {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * driver can update email and send him email for verify that
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @Route("/email_update")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function emailUpdate(Request $request)
    {
        $data = json_decode($request->getContent());
        $driverId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $driver = $this->getDoctrine()
            ->getRepository("AppBundle:Driver")
            ->find($driverId);
        if ($driver instanceof Driver) {
            $verifyToken = $this->get('app.token_generator')->generate(mt_rand(1, 5000));
            $driver->setVerifyEmailToken($verifyToken);
            $em->persist($driver);
            $em->flush();

            $router = $this->get('router');
            $recoveryPageUrl = $router->generate("app_driver_api_v1_driver_verify_email");
            $recoveryPageLink = $this->get("roapp_media.upload_manager")->getAbsoluteUrl($recoveryPageUrl).'/'.$data->email.'/'.$verifyToken;
            // send message
            $message = \Swift_Message::newInstance();
            $logoImageUrl = $message->embed(Swift_Image::fromPath('bundles/app/images/motor.png'));
            $bodyMessage = 'لطفا برای تایید ایمیل خود برروی لینک زیر کلیک نمایید.';
            $message->setSubject('Roapp Confirmation Email')
                ->setFrom($this->getParameter('mailer_user'))
                ->setTo($data->email)
                ->setBody(
                    $this->renderView(
                        ':security:email.html.twig',
                        [
                            'name' => $driver->getFullName(),
                            'bodyMessage' => $bodyMessage,
                            'link' => $recoveryPageLink,
                            'logoImageUrl' => $logoImageUrl,
                        ]
                    ),
                    'text/html'
                );

            $mailer = $this->get('mailer');

            $mailer->send($message);

            return new JsonResponse([], Response::HTTP_OK);
        } else {
            return new JsonResponse([], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @Route("/verify_email/{email}/{verifyToken}", name="app_driver_api_v1_driver_verify_email")
     * @param null $email
     * @param null $verifyToken
     * @internal param null $verifyToken
     * @return Response
     */
    public function verifyEmail($email = null, $verifyToken = null)
    {
        $em = $this->getDoctrine()->getManager();
        $driver = $this->getDoctrine()->getRepository("AppBundle:Driver")->findOneBy(['verifyEmailToken' => $verifyToken]);
        if ($driver instanceof Driver) {
            $driver->setVerifyEmailToken(null);
            $driver->setEmail($email);
            $em->persist($driver);
            $em->flush();
            $personDevice =  $this->getDoctrine()->getRepository("AppBundle:PersonDevice")->findOneBy(['person' => $driver]);
            if ($personDevice instanceof PersonDevice) {
                // push notification
                $data =
                    [
                        'registerId' => $personDevice->getNotificationToken(),
                        'topic' => 'verify_email',
                        'parameters' => [
                            'status' => true,
                        ],
                    ];
                $this->get("app.notification_service")->sendNotification($data);

                return $this->render(':driver/api:email_verify.html.twig');
            } else {
                // there is no device
                throw new NotFoundHttpException();
            }
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @Route("/upload_image")
     * @Method({"POST", "PUT"})
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImage(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $driverId = $this->getUser()->getId();
        $driver = $this->getDoctrine()->getRepository("AppBundle:Driver")->find($driverId);
        if ($driver instanceof Driver) {
            $data = json_decode($request->getContent());
            $image = (isset($data->image) && !empty(trim($data->image)) ? $data->image : null);
            $fullName = (isset($data->fullName) && !empty(trim($data->fullName)) ? $data->fullName : null);
            if ($image) {
                $extension = explode(',', $image);
                $extension = explode(';', substr($extension[0], 11))[0];
                $image = str_replace('data:image/'.$extension.';base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageData = base64_decode($image);
                $uploadDir = $this->getParameter('local_images_dir');
                $imageName = uniqid().'.'.$extension;
                $file = $uploadDir.$imageName;
                file_put_contents($file, $imageData);
                $driver->setImageName($imageName);
                $em->persist($driver);
                $em->flush();
            }
            if ($fullName) {
                $driver->setFullName($fullName);
                $em->persist($driver);
                $em->flush();
            }

            return new JsonResponse(Response::HTTP_OK);
        } else {
            return new JsonResponse(Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/delete_image")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     * @Method("DELETE")
     * @return JsonResponse
     */
    public function deleteImage()
    {
        $em = $this->getDoctrine()->getManager();
        $driverId = $this->getUser()->getId();
        $driver = $this->getDoctrine()->getRepository("AppBundle:Driver")->find($driverId);
        if ($driver instanceof Driver) {
            $imageName = $driver->getImageName();
            $imageAddress = $this->getParameter('local_images_dir');
            $file = $imageAddress.$imageName;
            unlink($file);
            $driver->setImageName(null);
            $em->persist($driver);
            $em->flush();

            return new JsonResponse(Response::HTTP_OK);
        } else {
            return new JsonResponse(Response::HTTP_FORBIDDEN);
        }
    }
}
