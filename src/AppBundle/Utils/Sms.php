<?php

namespace AppBundle\Utils;

use SoapClient;
use SoapFault;

/**
 * Class Sms
 * @package AppBundle\Utils
 */
class Sms
{
    /**
     * @var string
     */
    private $smsUsername;

    /**
     * @var string
     */
    private $smsPassword;

    /**
     * @var string
     */
    private $smsFrom;

    /**
     * Sms constructor.
     *
     * @param string $smsUsername
     * @param string $smsPassword
     * @param string $smsFrom
     */
    public function __construct($smsUsername, $smsPassword, $smsFrom)
    {
        $this->smsUsername = $smsUsername;
        $this->smsPassword = $smsPassword;
        $this->smsFrom = $smsFrom;
    }

    /**
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public function send($phone, $message)
    {
        ini_set("soap.wsdl_cache_enabled", "0");
        try {
            $client = new SoapClient(__DIR__.'/../../../app/Resources/wsdls/send_sms.xml');
            $parameters['username'] = $this->smsUsername;
            $parameters['password'] = $this->smsPassword;
            $parameters['from'] = $this->smsFrom;
            $parameters['to'] = array($phone);
            $parameters['text'] = $message;
            $parameters['isflash'] = true;
            $parameters['udh'] = "";
            $parameters['recId'] = array(0);
            $parameters['status'] = 0x0;

            return ($client->SendSms($parameters)->SendSmsResult == 1) ? true : false ;
        } catch (SoapFault $ex) {
            return false;
        }
    }
}
