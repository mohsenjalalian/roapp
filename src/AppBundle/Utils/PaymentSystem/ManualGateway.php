<?php

namespace AppBundle\Utils\PaymentSystem;

use AppBundle\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Validator\Constraints\Url;

class ManualGateway implements GatewayInterface
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function payConfirm(Payment $payment)
    {
        return Payment::STATUS_WAITING_FOR_APPROVE;
    }
    
    public function getRedirectUrl()
    {
        return 'app_customer_dashboard_gateway_manual';
    }

    public function getRedirectParameters(Payment $payment)
    {
        $parameters = [
            'payment' => $payment,
        ];
        
        return $parameters;
    }
}