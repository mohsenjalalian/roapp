<?php

namespace AppBundle\Utils;

class GCM
{
    const API_ACCESS_KEY = 'AIzaSyA3cuoKwPKS50g_U6DfKQ4ZTB6w_okX9rg';

    private $registrationIds;

    public function __construct()
    {
        $this->registrationIds = [
            "fA_Rd5yqojY:APA91bG2cRhP6Y4WY9XykS7t1sQUZgQ98mDMmxet91WFbWzInGFIr89EScUStUPy4mOnCYG5HSI0WBf2HX0-_-JusS6-jY_ol_xS0xQA1sV8O4whg9rHIoJvKAwHuysePwC82B1PWADe",
        ];
    }

    public function notify()
    {
        $msg = array
        (
            'message' => 'here is a message. message',
            'title' => 'This is a title. title',
            'subtitle' => 'This is a subtitle. subtitle',
            'tickerText' => 'Ticker text here...Ticker text here...Ticker text here',
            'vibrate' => 1,
            'sound' => 1,
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon',
        );
        $fields = array
        (
            'to' => $this->registrationIds[0],
            'data' => $msg,
        );

        $headers = array
        (
            'Authorization: key='.self::API_ACCESS_KEY,
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}