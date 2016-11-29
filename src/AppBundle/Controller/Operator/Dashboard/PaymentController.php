<?php

namespace AppBundle\Controller\Operator\Dashboard;

use AppBundle\Entity\Payment;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PaymentController
 * @package AppBundle\Controller\Operator\Dashboard
 * @Route("/payment")
 * @Security("has_role('ROLE_USER')")
 */
class PaymentController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/", name="app_operator_dashboard_payment_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $query = $this->getDoctrine()
            ->getRepository('AppBundle:Payment')
            ->createQueryBuilder('p')
            ->andWhere('p.status IN (:status)')
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
            "operator/dashboard/payment/index.html.twig",
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * @Route("/pay_confirm", name="app_operator_dashboard_payment_pay_confirm")
     * @Method("POST")
     * @param Request $request
     * @return NotFoundHttpException
     */
    public function payConfirm(Request $request)
    {
        $paymentId = $request->request->get("paymentId");
        try {
            $payment = $this->getDoctrine()
                ->getRepository("AppBundle:Payment")
                ->find($paymentId);
        } catch (\Exception $e) {
            return new NotFoundHttpException("page not found");
        }
        $this->get('app.payment_service')->operatorConfirm($payment);
        
        return new JsonResponse(true);
    }
    /**
     * @Route("/{id}", name="app_operator_dashboard_payment_show")
     * @Method("GET")
     * @param Payment $payment
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Payment $payment)
    {
        return $this->render(
            'operator/dashboard/payment/show.html.twig',
            [
                'payment' => $payment,
            ]
        );
    }
}