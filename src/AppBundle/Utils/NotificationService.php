<?php

namespace AppBundle\Utils;

/**
 * Class NotificationService
 * @package AppBundle\Utils
 */
class NotificationService
{
    /**
     * @param array $data
     * @return bool
     */
    public function sendNotification($data)
    {
        $sendUrl = 'https://fcm.googleapis.com/fcm/send';
        $headers = [
            'Authorization:key = AIzaSyBJaQ9dbnGXZbWoNu70nibNsUajGUj2GpA',
            'Content-Type: application/json',
        ];
        $registerId = $data['registerId'];
        $parameters = $data['parameters'];
        $fields = [
            'data' => [
                'body' => $parameters,
                'tag' => 'assignment',
            ],
            'registration_ids' => [$registerId],
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sendUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === false) {
            die("curl deny:".curl_error($ch));
        }
        curl_close($ch);

        return true;
        // send request to driver
    }
}
