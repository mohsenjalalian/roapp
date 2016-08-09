<?php

namespace AppBundle\Controller\Customer\Api\V1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Customer\Api\V1
 * @Route(path="default")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/index", name="customer_home")
     * @Security("has_role('ROLE_CUSTOMER')")
     * @param Request $request
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return new JsonResponse([
            'status' => true,
        ]);
    }
}
