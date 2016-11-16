<?php

namespace AppBundle\Utils;

use GuzzleHttp;

class NotificationService
{
    public function sendNotification($data)
    {
        $sendUrl = 'https://fcm.googleapis.com/fcm/send';
        $headers = [
            'Authorization:key = AIzaSyCb0DS_VTm25GZhfAOUQQDUImuX-rDnzM0',
            'Content-Type: application/json'
        ];
        $title = $data['title'];
        $text = $data['body'];
//        $topic = $data['topic'];
        $parameters = serialize($data['parameters']);
        $fields = [
            'notification' => [
                'title' => $title,
                'body' => $text,
                'tag' => $parameters
            ],
//            'to' => '/topics/'.$topic
            'registration_ids' => ['c4J1pXgRCYI:APA91bFkzHN9JAw_ewRkUJ5lY-RI_JG4noW6rxDn-yY3okgzw4_FpGgX9FFRG5IzTt7dARZbaHrPMY6wksYeIIJ6WahwRoR1lExDP8JMOYTshV4ROIhSu-TzJVAg9EU4vROdaY6iLlI4']
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
        dump($result);
        die();
        if ($result===FALSE) {
            die("curl deny:" . curl_error($ch));
        }
        curl_close($ch);
        
        return true;
        // send request to driver

    }
}