<?php

namespace AppBundle\Controller\Driver\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Driver\Dashboard
 */
class DefaultController extends Controller
{
    /**
     * @Security(expression="is_granted('ROLE_USER')")
     * @Route("/", name="customer_dashboard_front")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render(
            'driver/dashboard/default/index.html.twig'
        );
    }
}
