<?php

namespace AppBundle\Controller\Operator\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Operator\Dashboard
 * @Security("has_role('ROLE_USER')")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
          return $this->redirectToRoute("app_operator_dashboard_driver_list");
    }
}
