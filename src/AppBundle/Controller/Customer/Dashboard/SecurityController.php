<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Customer;

use AppBundle\Form\Customer\Dashboard\RegisterType;
use AppBundle\Form\Security\ForgetPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SecurityController
 * @package AppBundle\Controller\Customer\Dashboard
 */
class SecurityController extends Controller
{
    /**
     * @author Naghmeh Mashhadi
     * @Route("/login", name="app_customer_dashboard_security_login")
     * @param Request $request
     * @return Response This action displays and validates the login form
     * This action displays and validates the login form
     */
    public function loginAction(Request $request)
    {
        if ($this->isGranted("ROLE_CUSTOMER")) {
            return $this->redirectToRoute("app_customer_dashboard_shipment_index");
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
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
            ':customer/dashboard/security:login.html.twig',
            [
                // last username entered by the user
                'last_username' => $lastUsername,
                'error'         => $error,
                'forgetPasswordForm' => $forgetPasswordForm->createView(),
            ]
        );
    }

    /**
     * @author Naghmeh Mashhadi
     * @Route("/logout", name="logout")
     * @return \Symfony\Component\HttpFoundation\Response
     * This action is done after user logs out
     */
    public function logoutAction()
    {
        return new Response("You are logged out successfully.");
    }

    /**
     * @Route("/register", name="_register")
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(RegisterType::class, $customer);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()
                ->getManager();

            $activationToken = md5(uniqid("", true));
            $customer->setActivationToken($activationToken);

            $em->persist($customer);
            $em->flush();

            $this->get('logger')->info('customer activation token', [$activationToken]);

            return $this->render(':customer/dashboard/security:registered.html.twig');
        }

        return $this->render('customer/dashboard/security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/activation/{activationToken}")
     * @param String $activationToken
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activationAction($activationToken)
    {
        $customer = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Customer')
            ->findOneBy([
                'activationToken' => $activationToken,
                'isActive' => false,
            ]);

        if (!$customer instanceof Customer) {
            throw new NotFoundHttpException();
        }

        $customer->setIsActive(true);
        $this->getDoctrine()
            ->getManager()
            ->flush();

        return $this->redirectToRoute('app_customer_dashboard_security_login');
    }
}
