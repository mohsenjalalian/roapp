<?php

namespace AppBundle\Utils\PaymentSystem;

use AppBundle\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class ManualGateway
 * @package AppBundle\Utils\PaymentSystem
 */
class ManualGateway implements GatewayInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * ManualGateway constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param Payment $payment
     * @return int
     */
    public function payConfirm(Payment $payment)
    {
        return Payment::STATUS_WAITING_FOR_APPROVE;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return 'app_customer_dashboard_gateway_manual';
    }

    /**
     * @param Payment $payment
     * @return array
     */
    public function getRedirectParameters(Payment $payment)
    {
        $parameters = [
            'payment' => $payment->getId(),
        ];

        return $parameters;
    }
}
