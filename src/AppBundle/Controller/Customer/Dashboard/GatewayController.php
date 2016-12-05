<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Payment;
use AppBundle\Form\Customer\Dashboard\PaymentManualType;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Gateway controller.
 *
 * @Route("/gateway")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class GatewayController extends Controller
{
    /**
     * @method({"GET", "POST"})
     * @Route("/manual/{payment}", name="app_customer_dashboard_gateway_manual")
     * @param Request $request
     * @param Payment $payment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manualAction(Request $request, Payment $payment)
    {
        $form = $this->createForm(PaymentManualType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->get('app.payment_service')
                ->payConfirm($payment, $data);

            return $this->redirectToRoute(
                "app_customer_dashboard_shipment_index"
            );
        }

        return $this->render(
            "customer/dashboard/gateway/manual.html.twig",
            [
                'form' => $form->createView(),
            ]
        );
    }
}
