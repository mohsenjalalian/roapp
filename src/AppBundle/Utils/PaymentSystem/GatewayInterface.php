<?php

namespace AppBundle\Utils\PaymentSystem;

use AppBundle\Entity\Payment;

/**
 * Interface GatewayInterface
 * @package AppBundle\Utils\PaymentSystem
 */
interface GatewayInterface
{
    /**
     * @return mixed
     */
    public function getRedirectUrl();

    /**
     * @param Payment $payment
     * @return mixed
     */
    public function getRedirectParameters(Payment $payment);

    /**
     * @param Payment $payment
     * @return mixed
     */
    public function payConfirm(Payment $payment);
}
