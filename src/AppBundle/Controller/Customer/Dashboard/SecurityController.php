<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Customer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SecurityController extends Controller
{
    /**
     * @author Naghmeh Mashhadi
     * @Route("/login", name="login")
     * This action displays and validates the login form
     */
    public function loginAction(Request $request)
    {

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            ':customer/dashboard/security:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
            )
        );

    }

    /**
     * @author Naghmeh Mashhadi
     * @Route("/logout", name="logout")
     * This action is done after user logs out
     */
    public function logoutAction(Request $request)
    {
        return new Response("You are logged out successfully");
    }


}