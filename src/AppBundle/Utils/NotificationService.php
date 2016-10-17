<?php

namespace AppBundle\Utils;

use GuzzleHttp;

class NotificationService
{
    public function sendNotification($data)
    {
        $sendUrl = 'https://fcm.googleapis.com/fcm/send';
        $headers = [
            'Authorization:key = AIzaSyBJaQ9dbnGXZbWoNu70nibNsUajGUj2GpA',
            'Content-Type: application/json'
        ];
        $title = $data['title'];
        $text = $data['body'];
        $topic = $data['topic'];
        $parameters = serialize($data['parameters']);
        $fields = [
            'notification' => [
                'title' => $title,
                'body' => $text,
                'tag' => $parameters
            ],
            'to' => '/topics/'.$topic
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sendUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result===FALSE) {
            die("curl deny:" . curl_error($ch));
        }
        curl_close($ch);
        
        return true;
        // send request to driver

    }
}