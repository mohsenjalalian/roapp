<?php

namespace AppBundle\Utils\PaymentSystem;

use AppBundle\Entity\Payment;

interface GatewayInterface
{
    public function getRedirectUrl();
    public function getRedirectParameters(Payment $payment);
    public function payConfirm(Payment $payment);
}