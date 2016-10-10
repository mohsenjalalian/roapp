<?php

namespace AppBundle\Utils;


class SendNotification
{
    public function sendNotification($data)
    {
        $sendUrl = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Authorization:key = AIzaSyBJaQ9dbnGXZbWoNu70nibNsUajGUj2GpA',
            'Content-Type: application/json'
        );
        $title = $data['title'];
        $text = $data['body'];
        $topic = $data['topic'];
        $parameters = serialize($data['parameters']);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sendUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $fields = array(
            'notification' => array(
                'title' => $title,
                'body' => $text,
                'tag' => $parameters
            ),
            'to' => '/topics/'.$topic
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result===FALSE) {
            die("curl deny:" . curl_error($ch));
        }
        curl_close($ch);
        // send request to driver

        return true;
    }
}