<?php

namespace AppBundle\Controller\Customer\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Customer\Dashboard
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirectToRoute("app_customer_dashboard_shipment_index");
    }
}
