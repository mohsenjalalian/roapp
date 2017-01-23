<?php

namespace AppBundle\Controller\Security;

use AppBundle\Form\Security\ForgetPasswordType;
use AppBundle\Form\Security\RecoveryPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SecurityController
 * @package AppBundle\Controller\Security
 */
class SecurityController extends Controller
{
    /**
     * @Route("/forget_password", name="app_security_forget_password")
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function forgetPasswordAction(Request $request)
    {
        $forgetPasswordForm = $this->createForm(ForgetPasswordType::class);
        $forgetPasswordForm = $forgetPasswordForm->handleRequest($request);
        $currentRouteName = $forgetPasswordForm->get('current_route_name')->getData();
        if ($forgetPasswordForm->isSubmitted() && $forgetPasswordForm->isValid()) {
            $recoveryEmail = $forgetPasswordForm->get('recovery_email')->getData();
            if (stripos($currentRouteName, 'operator')) {
                $entityName = 'Operator';
            } else {
                $entityName = 'Customer';
            }
            $isValidEmail = $this
                ->getDoctrine()
                ->getRepository("AppBundle:".$entityName)
                ->isValidEmail($recoveryEmail);
            if ($isValidEmail) {
                $em = $this->getDoctrine()->getManager();
                $person = $this->getDoctrine()->getRepository("AppBundle:".$entityName)->findOneBy(['email' => $recoveryEmail]);
                $token = $this->get('app.token_generator')->generate(mt_rand(1, 5000));
                $person->setRecoveryPasswordToken($token);

                $em->persist($person);
                $em->flush();

                $translator = $this->get('translator');
                $router = $this->get('router');
                $recoveryPageUrl = $router->generate("app_security_recovery_password");
                $recoveryPageLink = $this->get("roapp_media.upload_manager")->getAbsoluteUrl($recoveryPageUrl).'/'.$entityName.'/'.$token;
                // send email
                $message = \Swift_Message::newInstance();
                $logoImageUrl = $message->embed(Swift_Image::fromPath('bundles/app/images/motor.png'));
                $message->setSubject($translator->trans('title_recovery_password_email'))
                    ->setFrom('roapp@narenjino.ir')
                    ->setTo($person->getEmail())
                    ->setBody(
                        $this->renderView(
                            ':security:email.html.twig',
                            [
                                'name' => $person->getFullName(),
                                'link' => $recoveryPageLink,
                                'logoImageUrl' => $logoImageUrl,
                            ]
                        ),
                        'text/html'
                    );

                $mailer = $this->get('mailer');

                $mailer->send($message);

                $this->addFlash('send_recovery_link_success', $translator->trans('please_check_your_email'));

                return $this->redirectToRoute($currentRouteName);
            } else {
                $translator = $this->get('translator');
                $this->addFlash('unsuccess_send_recovery_link', $translator->trans('invalid_email'));

                return $this->redirectToRoute($currentRouteName);
            }
        } else {
            return $this->redirectToRoute($currentRouteName);
        }
    }

    /**
     * @Route("/recovery_password/{entityName}/{token}", name="app_security_recovery_password")
     * @param Request $request
     * @param string  $entityName
     * @param string  $token
     * @return NotFoundHttpException
     */
    public function recoveryPassword(Request $request, $entityName = null, $token = null)
    {
        $isValidRecoveryToken = $this->getDoctrine()->getRepository("AppBundle:".$entityName)->isValidRecoveryPasswordToken($token);
        if ($isValidRecoveryToken) {
            $person = $this->getDoctrine()->getRepository("AppBundle:".$entityName)->findOneBy(['recoveryPasswordToken' => $token]);
            if ($person) {
                $recoveryPasswordForm = $this->createForm(
                    RecoveryPasswordType::class,
                    [],
                    [
                        'action' => $this->generateUrl('app_security_recovery_password', ['entityName' => $entityName, 'token' => $token]),
                    ]
                );
                $recoveryPasswordForm = $recoveryPasswordForm->handleRequest($request);
                if ($recoveryPasswordForm->isSubmitted() && $recoveryPasswordForm->isValid()) {
                    $newPassword = $recoveryPasswordForm->get('newPassword')->getData();
                    if (strlen(trim($newPassword) != 0)) {
                        $em = $this->getDoctrine()->getManager();
                        $person->setPassword($recoveryPasswordForm->get('newPassword')->getData());
                        $person->setRecoveryPasswordToken(null);

                        $em->persist($person);
                        $em->flush();

                        return $this->redirectToRoute('app_'.strtolower($entityName).'_dashboard_security_login');
                    } else {
                        $translator = $this->get('translator');
                        $this->addFlash('invalid_data', $translator->trans('invalid_input'));

                        return $this->redirectToRoute('app_security_recovery_password', ['entityName' => $entityName, 'token' => $person->getRecoveryToken() ]);
                    }
                }

                return $this->render(
                    ':security:recovery_password.html.twig',
                    [
                        'recoveryPasswordForm' => $recoveryPasswordForm->createView(),
                    ]
                );
            } else {
                throw new NotFoundHttpException();
            }
        } else {
            throw new NotFoundHttpException();
        }
    }
}
