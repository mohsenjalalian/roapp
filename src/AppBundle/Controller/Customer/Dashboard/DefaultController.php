<?php

namespace AppBundle\Controller\Customer\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Customer\Dashboard
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
//        return $this->render(':customer/dashboard/default:index.html.twig');
        return $this->redirectToRoute("app_customer_dashboard_shipment_index");

    }
}