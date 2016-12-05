<?php

namespace AppBundle\Controller\Customer\Api\V1;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Customer\Api\V1
 * @Route(path="default")
 */
class DefaultController extends Controller
{
    /**
     * @Route(path="/", name="customer_home")
     * @Security("has_role('ROLE_CUSTOMER')")
     * @return JsonResponse
     */
    public function indexAction()
    {
        // replace this example code with whatever you need
        return new JsonResponse([
            'status' => true,
        ]);
    }
}
