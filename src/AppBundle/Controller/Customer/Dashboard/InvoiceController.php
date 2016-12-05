<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Invoice;
use AppBundle\Form\Customer\Dashboard\PaymentStyle;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Invoice controller.
 *
 * @Route("/invoice")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class InvoiceController extends Controller
{
    /**
     * @method({"GET", "POST"})
     * @Route("/checkout/{id}", name="app_customer_dashboard_invoice_checkout")
     * @param Invoice $invoice
     * @return Response
     */
    public function checkoutAction(Invoice $invoice)
    {
        if ($invoice->getStatus() != Invoice::STATUS_PAID) {
            $shipment = $this->getDoctrine()
                ->getRepository("AppBundle:Shipment")
                ->findBy(['invoice' => $invoice]);
            $form = $this->createForm(
                PaymentStyle::class,
                [
                    'invoice_id' => $invoice->getId(),
                ],
                [
                    'action' => $this->generateUrl('app_customer_dashboard_payment_pay'),
                ]
            );

            return $this->render(
                "customer/dashboard/invoice/checkout.html.twig",
                [
                    "invoice" => $invoice,
                    "shipment" => $shipment[0],
                    'form' => $form->createView(),
                ]
            );
        } else {
            return $this->redirectToRoute('app_customer_dashboard_shipment_index');
        }
    }
}
