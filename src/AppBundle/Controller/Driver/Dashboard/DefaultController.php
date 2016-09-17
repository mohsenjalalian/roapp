<?php

namespace AppBundle\Controller\Driver\Dashboard;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="customer_dashboard_front")
     * @Security(expression="is_granted('ROLE_USER')")
     */
    public function indexAction()
    {
        return $this->render(
            'driver/dashboard/default/index.html.twig'
        );
    }
}