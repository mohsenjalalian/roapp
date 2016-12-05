<?php

namespace AppBundle\Utils\Services;

use AppBundle\Entity\Invoice;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Person;
use AppBundle\Entity\Shipment;
use AppBundle\Utils\PaymentSystem\GatewayInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class PaymentService
 * @package AppBundle\Utils\Services
 */
class PaymentService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var GatewayInterface[]
     */
    private $gateways;

    /**
     * PaymentService constructor.
     * @param EntityManager $entityManager
     * @param Router        $router
     */
    public function __construct(EntityManager $entityManager, Router $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @param string  $paymentMethod
     * @param Invoice $invoice
     * @param Person  $person
     * @return RedirectResponse
     */
    public function pay($paymentMethod, Invoice $invoice, Person $person)
    {
        $em = $this->entityManager;
        $payment = $em->getRepository("AppBundle:Payment")
            ->findOpenPayment($invoice);
        if (!$payment instanceof Payment) {
            $payment = new Payment();
            $payment->setInvoice($invoice);
            $payment->setMethod($paymentMethod);
            $payment->setCreatedDate(new \DateTime());
            $payment->setStatus(Payment::STATUS_WAITING_FOR_PAYMENT);
            $payment->setShipment($invoice->getShipment());
            $payment->setPerson($person);

            $em->persist($payment);
            $em->flush();
        }
        $gatewayService = $this->gateways['app.gateway.'.$paymentMethod];
        $parameters =  $gatewayService->getRedirectParameters($payment);
        $url = $gatewayService->getRedirectUrl();
        if (strpos($url, 'http') !== false) {
            $queryString = "";
            $loopCounter = 1;
            foreach ($parameters as $key => $val) {
                if ($loopCounter == 1) {
                    $queryString = $queryString.$key."=".$val;
                } else {
                    $queryString = $queryString."&".$key."=".$val;
                }
                $loopCounter++ ;
            }

            return new RedirectResponse($url."?".$queryString);
        } else {
            return new RedirectResponse($this->router->generate($url, $parameters));
        }
    }

    /**
     * @param Payment $payment
     * @param array   $data
     * @return bool
     */
    public function payConfirm(Payment $payment, $data)
    {
        $em = $this->entityManager;
        $gatewayService = $this->gateways['app.gateway.'.$payment->getMethod()];
        $payConfirmResult = $gatewayService->payConfirm($payment);
        switch ($payConfirmResult) {
            case Payment::STATUS_WAITING_FOR_APPROVE:
                $payment->setStatus(Payment::STATUS_WAITING_FOR_APPROVE);
                $payment->setPaidAt(new \DateTime());
                $payment->setData($data);
                break;
            case Payment::STATUS_APPROVED:
                $payment->setStatus(Payment::STATUS_APPROVED);
                $payment->getInvoice()->setStatus(Invoice::STATUS_PAID);
                $payment->setPaidAt(new \DateTime());
                $payment->setData($data);
                $shipment = $this->entityManager->getRepository("AppBundle:Shipment")
                    ->findOneBy(
                        [
                            'invoice' => $payment->getInvoice(),
                        ]
                    );
                $shipment->setStatus(Shipment::STATUS_NOT_ASSIGNED);

                $em->persist($shipment);

                break;
            case Payment::STATUS_FAILED:
                $payment->setStatus(Payment::STATUS_FAILED);
                $payment->setData($data);
                break;
            case Payment::STATUS_CANCEL:
                $payment->setStatus(Payment::STATUS_CANCEL);
                $payment->setData($data);
                break;
        }
        $em->persist($payment);
        $em->flush();

        return true;
    }

    /**
     * @param Payment $payment
     */
    public function operatorConfirm(Payment $payment)
    {
        $em = $this->entityManager;
        $payment->setStatus(Payment::STATUS_APPROVED);
        //@TODO move shipment related code to event listener
        $payment->getShipment()->setStatus(Shipment::STATUS_NOT_ASSIGNED);
        $payment->getInvoice()->setStatus(Invoice::STATUS_PAID);

        $em->persist($payment);
        $em->flush();
    }

    /**
     * @param string           $gatewayServiceName
     * @param GatewayInterface $gateway
     */
    public function addGateway($gatewayServiceName, GatewayInterface $gateway)
    {
        $this->gateways[$gatewayServiceName] = $gateway;
    }
}
