<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Form\Security\ForgetPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SecurityController
 * @package AppBundle\Controller\Operator\Dashboard
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login",name="app_operator_dashboard_security_login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute("app_operator_dashboard_shipment_list");
        }
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $currentRouteName = $request->get('_route');
        $forgetPasswordForm = $this->createForm(
            ForgetPasswordType::class,
            [
                'current_route_name' => $currentRouteName,
            ],
            [
                'action' => $this->generateUrl('app_security_forget_password'),
            ]
        );

        return $this->render(
            'operator/dashboard/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'forgetPasswordForm' => $forgetPasswordForm->createView(),
            ]
        );
    }

    /**
     * @Route("/logout", name="app_operator_dashboard_security_logout")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logoutAction()
    {
        return $this->render(':operator/dashboard/security:login.html.twig');
    }
}
