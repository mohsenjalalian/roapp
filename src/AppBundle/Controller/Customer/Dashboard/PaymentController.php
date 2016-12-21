<?php

namespace AppBundle\Controller\Customer\Dashboard;

use AppBundle\Entity\Payment;
use AppBundle\Form\Customer\Dashboard\PaymentManualType;
use AppBundle\Form\Customer\Dashboard\PaymentStyle;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Payment controller.
 *
 * @Route("/payment")
 * @Security("has_role('ROLE_CUSTOMER')")
 */
class PaymentController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/", name="app_dashboard_customer_payment_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $userId = $this->getUser()->getId();
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Payment')
            ->createQueryBuilder('p')
            ->where("p.person=:userId")
            ->andWhere('p.status IN (:status)')
            ->setParameter('userId', $userId)
            ->setParameter(
                'status',
                [
                    Payment::STATUS_WAITING_FOR_APPROVE,
                    Payment::STATUS_APPROVED,
                    Payment::STATUS_FAILED,
                    Payment::STATUS_CANCEL,
                ]
            )
            ->orderBy('p.id', 'Asc')
            ->getQuery()
        ;
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render(
            "customer/dashboard/payment/index.html.twig",
            [
                'pagination' => $pagination,
            ]
        );
    }
    /**
     * @method({"GET","POST"})
     * @Route("/payment_manual/{payment}", name="app_customer_dashboard_payment_payment_manual")
     * @param Request $request
     * @param Payment $payment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function paymentManualAction(Request $request, Payment $payment)
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
            "customer/dashboard/payment/payment_manual.html.twig",
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @method({"POST"})
     * @Route("/pay", name="app_customer_dashboard_payment_pay")
     * @param Request $request
     * @return RedirectResponse
     */
    public function payAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(PaymentStyle::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $paymentMethod = $form->get('method')->getData();
            $invoiceId = $form->get('invoice_id')->getData();
            $invoice = $em->getRepository("AppBundle:AbstractInvoice")
                ->find($invoiceId);
            $result = $this->get('app.payment_service')->pay($paymentMethod, $invoice, $this->getUser());

            return new RedirectResponse($result->getTargetUrl());
        } else {
            return $this->redirectToRoute("app_customer_dashboard_invoice_checkout", ['invoiceId' => $request->request->get('payment_style')['invoice_id']]);
        }
    }

    /**
     * @Method("GET")
     * @Route("/{id}", name="app_customer_dashboard_payment_show")
     * @param Payment $payment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Payment $payment)
    {
        return $this->render(
            'customer/dashboard/payment/show.html.twig',
            [
                'payment' => $payment,
            ]
        );
    }
}
