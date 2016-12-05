<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class SecurityController
 * @package AppBundle\Controller\Operator\Dashboard
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login",name="app_operator_dashboard_security_login")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute("app_operator_dashboard_shipment_list");
        }
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'operator/dashboard/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
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
